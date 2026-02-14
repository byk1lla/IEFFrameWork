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
    protected static bool $useUuid = false;
    protected static array $fillable = [];
    protected static array $hidden = ['password_hash'];

    protected array $attributes = [];
    protected array $original = [];

    // Query Builder State
    protected array $wheres = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $params = [];

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
        $results = self::db()->fetchAll($sql);
        return array_map(fn($item) => new static($item), $results);
    }

    public static function find($id): ?static
    {
        $result = static::query()->where(static::$primaryKey, $id)->first();
        return $result;
    }

    public static function findOrFail($id): static
    {
        $result = self::find($id);
        if (!$result) {
            throw new \Exception("Record not found");
        }
        return $result;
    }

    public static function create(array $data): static
    {
        // Filter by fillable
        if (!empty(static::$fillable)) {
            $data = array_intersect_key($data, array_flip(static::$fillable));
        }

        if (static::$useUuid && !isset($data[static::$primaryKey])) {
            $data[static::$primaryKey] = Uuid::v4()->toRfc4122();
        } elseif (!static::$useUuid) {
            unset($data[static::$primaryKey]);
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})";
        $db = self::db();
        $db->execute($sql, array_values($data));

        $id = $data[static::$primaryKey] ?? $db->getPdo()->lastInsertId();
        return static::find($id);
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

    // --- Fluent Query Builder ---

    public static function query(): static
    {
        return new static();
    }

    public function where(string $column, $value, string $operator = '='): self
    {
        $this->wheres[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, $value, string $operator = '='): self
    {
        $this->wheres[] = "OR {$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM " . static::$table;
        $sql .= $this->buildQueryChunks();

        return self::db()->fetchAll($sql, $this->params);
    }

    public function first(): ?static
    {
        $this->limit = 1;
        $results = $this->get();
        $data = $results[0] ?? null;
        return $data ? new static($data) : null;
    }

    protected function buildQueryChunks(): string
    {
        $sql = "";

        // Soft Delete check
        if (property_exists($this, 'useSoftDeletes') && static::$useSoftDeletes) {
            $this->where('deleted_at', null, 'IS');
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
            // Fix OR prefixes
            $sql = str_replace('WHERE OR', 'WHERE', $sql);
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    // --- Relationships ---

    protected function hasMany(string $relatedClass, string $foreignKey, string $localKey = 'id'): array
    {
        return $relatedClass::where($foreignKey, $this->attributes[$localKey]);
    }

    protected function belongsTo(string $relatedClass, string $foreignKey, string $ownerKey = 'id'): ?array
    {
        return $relatedClass::find($this->attributes[$foreignKey]);
    }

    // --- Soft Deletes ---

    public function softDelete(): bool
    {
        return static::update($this->attributes[static::$primaryKey], [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function withTrashed(): self
    {
        $instance = new static();
        // Conceptually, would need to bypass deleted_at filter in buildQueryChunks
        return $instance;
    }
}
