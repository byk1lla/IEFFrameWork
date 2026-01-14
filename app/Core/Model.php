<?php
/**
 * Temel Model Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

use Symfony\Component\Uid\Uuid;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static bool $useUuid = true;
    protected static array $fillable = [];
    protected static array $hidden = ['password_hash'];
    
    protected array $attributes = [];
    protected array $original = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$fillable) || empty(static::$fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function toArray(): array
    {
        $data = $this->attributes;
        foreach (static::$hidden as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    public static function getTable(): string
    {
        return static::$table;
    }

    protected static function db(): Database
    {
        return Database::getInstance();
    }

    public static function all(): array
    {
        $sql = "SELECT * FROM " . static::$table;
        return self::db()->fetchAll($sql);
    }

    public static function find($id): ?array
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        return self::db()->fetch($sql, [$id]);
    }

    public static function findOrFail($id): array
    {
        $result = self::find($id);
        if (!$result) {
            throw new \Exception("Record not found");
        }
        return $result;
    }

    public static function where(string $column, $value, string $operator = '='): array
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ?";
        return self::db()->fetchAll($sql, [$value]);
    }

    public static function whereFirst(string $column, $value, string $operator = '='): ?array
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ? LIMIT 1";
        return self::db()->fetch($sql, [$value]);
    }

    public static function create(array $data): string
    {
        if (static::$useUuid && !isset($data[static::$primaryKey])) {
            $data[static::$primaryKey] = Uuid::v4()->toRfc4122();
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})";
        self::db()->execute($sql, array_values($data));

        return $data[static::$primaryKey] ?? self::db()->lastInsertId();
    }

    public static function update($id, array $data): bool
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE " . static::$table . " SET {$set} WHERE " . static::$primaryKey . " = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        return self::db()->execute($sql, $values) > 0;
    }

    public static function delete($id): bool
    {
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        return self::db()->execute($sql, [$id]) > 0;
    }

    public static function count(string $column = '*', ?string $where = null, array $params = []): int
    {
        $sql = "SELECT COUNT({$column}) as total FROM " . static::$table;
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $result = self::db()->fetch($sql, $params);
        return (int) ($result['total'] ?? 0);
    }

    public static function paginate(int $page = 1, int $perPage = 20, ?string $where = null, array $params = [], string $orderBy = 'created_at DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        
        $countSql = "SELECT COUNT(*) as total FROM " . static::$table;
        if ($where) {
            $countSql .= " WHERE {$where}";
        }
        $totalResult = self::db()->fetch($countSql, $params);
        $total = (int) ($totalResult['total'] ?? 0);

        $sql = "SELECT * FROM " . static::$table;
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        
        $data = self::db()->fetchAll($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }
}
