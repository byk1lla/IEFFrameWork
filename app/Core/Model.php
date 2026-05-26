<?php
/**
 * Model — temel ORM modeli.
 *
 * Statik facade (Eloquent benzeri) + fluent query builder:
 *   User::where('email', $e)->first()
 *   User::query()->where('role', 'admin')->orderBy('id', 'DESC')->get()
 *   User::find($id)
 *   User::create(['email' => '...', 'password' => '...'])
 *
 * v2.0 düzeltmeleri:
 *   - $hidden default boş array (önceki: hatalı 'password_hash')
 *   - Static where()/orderBy() facade'i eklendi (Auth artık doğru çalışır)
 *   - OR prefix hack'i temiz `whereBuilder` ile değiştirildi
 *   - update/save instance + static varyantı
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\Uid\Uuid;

abstract class Model
{
    protected static string $table       = '';
    protected static string $primaryKey  = 'id';
    protected static bool   $useUuid     = false;
    protected static bool   $timestamps  = true;
    /** @var array<string> */
    protected static array  $fillable    = [];
    /** @var array<string> */
    protected static array  $hidden      = [];
    /** @var array<string> */
    protected static array  $casts       = [];

    /** @var array<string,mixed> */
    protected array $attributes = [];
    /** @var array<string,mixed> */
    protected array $original   = [];

    // Query builder state (instance)
    /** @var array<int,array{type:string,sql:string,boolean:string}> */
    protected array $wheres   = [];
    /** @var array<int,string> */
    protected array $orders   = [];
    protected ?int  $limit    = null;
    protected ?int  $offset   = null;
    /** @var array<int,mixed> */
    protected array $bindings = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    // ─── Erişim ────────────────────────────────────────────────────
    public function __get(string $key): mixed                  { return $this->attributes[$key] ?? null; }
    public function __set(string $key, mixed $value): void     { $this->attributes[$key] = $value; }
    public function __isset(string $key): bool                 { return isset($this->attributes[$key]); }
    public function getAttributes(): array                     { return $this->attributes; }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $k => $v) {
            if (empty(static::$fillable) || in_array($k, static::$fillable, true) || $k === static::$primaryKey || in_array($k, ['created_at', 'updated_at'], true)) {
                $this->attributes[$k] = $v;
            }
        }
        return $this;
    }

    public function toArray(): array
    {
        $data = $this->attributes;
        foreach (static::$hidden as $h) unset($data[$h]);
        return $data;
    }

    // ─── Yardımcılar ───────────────────────────────────────────────
    public static function getTable(): string  { return static::$table; }
    public static function getKey(): string    { return static::$primaryKey; }

    protected static function db(): Database
    {
        return Database::getInstance();
    }

    public static function query(): static
    {
        return new static();
    }

    // ─── Static facade (Eloquent benzeri) ─────────────────────────
    public static function where(string $column, mixed $value, string $operator = '='): static
    {
        return static::query()->whereCondition($column, $value, $operator, 'AND');
    }

    public static function orderByStatic(string $column, string $direction = 'ASC'): static
    {
        return static::query()->orderBy($column, $direction);
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function find(mixed $id): ?static
    {
        return static::query()->whereCondition(static::$primaryKey, $id, '=', 'AND')->first();
    }

    public static function findOrFail(mixed $id): static
    {
        $r = static::find($id);
        if (!$r) throw new \RuntimeException(static::class . ' kayıt bulunamadı: ' . $id);
        return $r;
    }

    public static function create(array $data): static
    {
        $data = self::filterFillable($data);

        if (static::$useUuid && !isset($data[static::$primaryKey])) {
            $data[static::$primaryKey] = Uuid::v4()->toRfc4122();
        } elseif (!static::$useUuid) {
            unset($data[static::$primaryKey]);
        }

        if (static::$timestamps) {
            $data['created_at'] ??= date('Y-m-d H:i:s');
            $data['updated_at'] ??= date('Y-m-d H:i:s');
        }

        $cols = '`' . implode('`, `', array_keys($data)) . '`';
        $ph   = implode(', ', array_fill(0, count($data), '?'));
        $sql  = "INSERT INTO `" . static::$table . "` ($cols) VALUES ($ph)";

        $db = self::db();
        $db->execute($sql, array_values($data));

        $id = static::$useUuid ? $data[static::$primaryKey] : $db->lastInsertId();
        return static::find($id) ?? new static($data);
    }

    public static function updateById(mixed $id, array $data): bool
    {
        $data = self::filterFillable($data);
        if (static::$timestamps) $data['updated_at'] = date('Y-m-d H:i:s');
        if (!$data) return false;

        $set = '`' . implode('` = ?, `', array_keys($data)) . '` = ?';
        $sql = "UPDATE `" . static::$table . "` SET $set WHERE `" . static::$primaryKey . "` = ?";

        $vals   = array_values($data);
        $vals[] = $id;
        return self::db()->execute($sql, $vals) > 0;
    }

    public static function deleteById(mixed $id): bool
    {
        $sql = "DELETE FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?";
        return self::db()->execute($sql, [$id]) > 0;
    }

    public static function count(): int
    {
        $row = self::db()->fetch("SELECT COUNT(*) AS c FROM `" . static::$table . "`");
        return (int) ($row['c'] ?? 0);
    }

    // ─── Instance kaydetme ─────────────────────────────────────────
    public function save(): bool
    {
        $id = $this->attributes[static::$primaryKey] ?? null;
        if ($id !== null && static::find($id)) {
            return static::updateById($id, $this->attributes);
        }
        $new = static::create($this->attributes);
        $this->attributes = $new->attributes;
        return true;
    }

    public function delete(): bool
    {
        $id = $this->attributes[static::$primaryKey] ?? null;
        return $id !== null && static::deleteById($id);
    }

    // ─── Fluent Query Builder (instance) ───────────────────────────
    public function whereCondition(string $column, mixed $value, string $operator = '=', string $boolean = 'AND'): self
    {
        $this->wheres[]   = ['sql' => "`$column` $operator ?", 'boolean' => strtoupper($boolean)];
        $this->bindings[] = $value;
        return $this;
    }

    public function whereChain(string $column, mixed $value, string $operator = '='): self
    {
        return $this->whereCondition($column, $value, $operator, 'AND');
    }

    public function orWhere(string $column, mixed $value, string $operator = '='): self
    {
        return $this->whereCondition($column, $value, $operator, 'OR');
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['sql' => "`$column` IS NULL", 'boolean' => 'AND'];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['sql' => "`$column` IS NOT NULL", 'boolean' => 'AND'];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = "`$column` $dir";
        return $this;
    }

    public function take(int $limit): self    { $this->limit  = $limit;  return $this; }
    public function skip(int $offset): self   { $this->offset = $offset; return $this; }

    public function get(): array
    {
        $sql  = "SELECT * FROM `" . static::$table . "`" . $this->buildClauses();
        $rows = self::db()->fetchAll($sql, $this->bindings);
        return array_map(fn ($r) => new static($r), $rows);
    }

    public function first(): ?static
    {
        $this->limit = 1;
        $sql  = "SELECT * FROM `" . static::$table . "`" . $this->buildClauses();
        $row  = self::db()->fetch($sql, $this->bindings);
        return $row ? new static($row) : null;
    }

    public function exists(): bool
    {
        $sql = "SELECT 1 FROM `" . static::$table . "`" . $this->buildClauses() . " LIMIT 1";
        return self::db()->fetch($sql, $this->bindings) !== null;
    }

    protected function buildClauses(): string
    {
        $sql = '';

        if ($this->wheres) {
            $parts = [];
            foreach ($this->wheres as $i => $w) {
                $parts[] = ($i === 0 ? '' : "{$w['boolean']} ") . $w['sql'];
            }
            $sql .= ' WHERE ' . implode(' ', $parts);
        }

        if ($this->orders) $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        if ($this->limit !== null)  $sql .= ' LIMIT '  . $this->limit;
        if ($this->offset !== null) $sql .= ' OFFSET ' . $this->offset;

        return $sql;
    }

    // ─── Fillable filtresi ─────────────────────────────────────────
    protected static function filterFillable(array $data): array
    {
        if (empty(static::$fillable)) return $data;
        return array_intersect_key($data, array_flip(static::$fillable));
    }
}
