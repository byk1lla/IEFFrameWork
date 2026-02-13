<?php
/**
 * Veritabanı Bağlantı Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    protected static ?Database $instance = null;
    protected ?PDO $pdo = null;
    protected array $config = [];

    private function __construct()
    {
        $this->config = Config::get('database');
        $this->connect();
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function connect(): void
    {
        $driver = $this->config['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $this->pdo = new PDO(
                    'sqlite:' . ($this->config['path'] ?? STORAGE_PATH . '/database.sqlite')
                );
            } else {
                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $driver,
                    $this->config['host'] ?? 'localhost',
                    $this->config['port'] ?? 3306,
                    $this->config['database'] ?? '',
                    $this->config['charset'] ?? 'utf8mb4'
                );

                $this->pdo = new PDO(
                    $dsn,
                    $this->config['username'] ?? 'root',
                    $this->config['password'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            }
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $start = microtime(true);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $time = microtime(true) - $start;

        DebugBar::getInstance()->logQuery($sql, $params, $time);

        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function transaction(callable $callback)
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
