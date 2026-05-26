<?php
/**
 * Database — PDO singleton + sorgu helper'ları (debug bar log entegre).
 *
 * Bug fix'ler (v1.2 → v2.0):
 *   - default driver MySQL (önceki: sqlite)
 *   - array_map('strval', $params) hack'i kaldırıldı (MySQL int compare doğru)
 *   - PDO options config'den okunur
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    protected static ?Database $instance = null;

    protected PDO   $pdo;
    protected array $config;
    protected string $driver;

    private function __construct()
    {
        $this->config = Config::get('database', []);
        $this->driver = (string) ($this->config['driver'] ?? 'mysql');
        $this->connect();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public function getPdo(): PDO       { return $this->pdo; }
    public function getDriver(): string { return $this->driver; }

    protected function connect(): void
    {
        $options = (array) ($this->config['options'] ?? [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        try {
            if ($this->driver === 'sqlite') {
                $path = (string) ($this->config['path'] ?? STORAGE_PATH . '/database.sqlite');
                if (!is_dir(dirname($path))) @mkdir(dirname($path), 0775, true);
                $this->pdo = new PDO("sqlite:$path", null, null, $options);
                $this->pdo->exec('PRAGMA foreign_keys = ON');
                return;
            }

            // MySQL / MariaDB
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $this->driver,
                $this->config['host']     ?? '127.0.0.1',
                (int) ($this->config['port'] ?? 3306),
                $this->config['database'] ?? '',
                $this->config['charset']  ?? 'utf8mb4'
            );

            $this->pdo = new PDO(
                $dsn,
                $this->config['username'] ?? 'root',
                $this->config['password'] ?? '',
                $options
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Veritabanı bağlantısı başarısız: ' . $e->getMessage(), 0, $e);
        }
    }

    // ─── Sorgu çalıştırıcılar ──────────────────────────────────────
    public function query(string $sql, array $params = []): PDOStatement
    {
        $start = microtime(true);
        $stmt  = $this->pdo->prepare($sql);
        $stmt->execute($params);
        DebugBar::getInstance()->logQuery($sql, $params, microtime(true) - $start);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function exec(string $sql): int
    {
        $start = microtime(true);
        $rows  = $this->pdo->exec($sql);
        DebugBar::getInstance()->logQuery($sql, [], microtime(true) - $start);
        return (int) $rows;
    }

    public function lastInsertId(): string
    {
        return (string) $this->pdo->lastInsertId();
    }

    // ─── Transactions ──────────────────────────────────────────────
    public function beginTransaction(): bool { return $this->pdo->beginTransaction(); }
    public function commit(): bool           { return $this->pdo->commit(); }
    public function rollBack(): bool         { return $this->pdo->rollBack(); }
    public function inTransaction(): bool    { return $this->pdo->inTransaction(); }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($this->inTransaction()) $this->rollBack();
            throw $e;
        }
    }
}
