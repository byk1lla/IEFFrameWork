<?php
/**
 * Blueprint — tablo şema tanımı (Schema Builder DSL).
 *
 * Schema::create('users', function (Blueprint $t) {
 *     $t->id();
 *     $t->string('email', 191)->unique();
 *     $t->string('password');
 *     $t->string('role')->default('user');
 *     $t->timestamps();
 * });
 *
 * Driver-aware (mysql / sqlite). Database::getDriver()'a göre SQL üretir.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Blueprint
{
    protected string $table;
    protected string $driver;
    /** @var array<int,array<string,mixed>> */
    protected array  $columns = [];
    /** @var array<int,array<string,mixed>> */
    protected array  $indexes = [];
    protected string $engine    = 'InnoDB';
    protected string $charset   = 'utf8mb4';
    protected string $collation = 'utf8mb4_unicode_ci';

    public function __construct(string $table, string $driver = 'mysql')
    {
        $this->table  = $table;
        $this->driver = $driver;
    }

    // ─── Kolon ekleyiciler (akıcı: ->default(), ->nullable(), ->unique()) ──
    public function id(string $name = 'id'): self
    {
        return $this->addColumn($name, 'BIGINT UNSIGNED', [
            'auto_increment' => true,
            'primary'        => true,
            'nullable'       => false,
        ]);
    }

    public function uuid(string $name = 'id', bool $primary = true): self
    {
        return $this->addColumn($name, 'CHAR(36)', [
            'primary'  => $primary,
            'nullable' => false,
        ]);
    }

    public function string(string $name, int $length = 255): self
    {
        return $this->addColumn($name, "VARCHAR($length)");
    }

    public function text(string $name): self           { return $this->addColumn($name, 'TEXT'); }
    public function longText(string $name): self       { return $this->addColumn($name, 'LONGTEXT'); }
    public function integer(string $name): self        { return $this->addColumn($name, 'INT'); }
    public function bigInteger(string $name): self     { return $this->addColumn($name, 'BIGINT'); }
    public function unsignedBigInteger(string $name): self { return $this->addColumn($name, 'BIGINT UNSIGNED'); }
    public function tinyInteger(string $name): self    { return $this->addColumn($name, 'TINYINT'); }
    public function boolean(string $name): self        { return $this->addColumn($name, 'TINYINT(1)', ['default' => 0]); }
    public function decimal(string $name, int $p = 10, int $s = 2): self { return $this->addColumn($name, "DECIMAL($p,$s)"); }
    public function float(string $name): self          { return $this->addColumn($name, 'FLOAT'); }
    public function json(string $name): self           { return $this->addColumn($name, 'JSON'); }
    public function date(string $name): self           { return $this->addColumn($name, 'DATE'); }
    public function time(string $name): self           { return $this->addColumn($name, 'TIME'); }
    public function dateTime(string $name): self       { return $this->addColumn($name, 'DATETIME'); }
    public function timestamp(string $name): self      { return $this->addColumn($name, 'TIMESTAMP NULL'); }

    public function enum(string $name, array $values): self
    {
        $list = implode(',', array_map(fn ($v) => "'" . str_replace("'", "''", $v) . "'", $values));
        return $this->addColumn($name, "ENUM($list)");
    }

    public function timestamps(): self
    {
        $this->addColumn('created_at', 'TIMESTAMP', ['default' => 'CURRENT_TIMESTAMP', 'nullable' => false]);
        $this->addColumn('updated_at', 'TIMESTAMP', ['default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP', 'nullable' => false]);
        return $this;
    }

    public function softDeletes(): self
    {
        return $this->addColumn('deleted_at', 'TIMESTAMP', ['nullable' => true]);
    }

    // ─── Modifier'lar — son eklenen kolona uygulanır ───────────────
    public function nullable(bool $value = true): self  { return $this->modifyLast('nullable',  $value); }
    public function default(mixed $value): self         { return $this->modifyLast('default',   $value); }
    public function unique(): self                      { $this->indexes[] = ['type' => 'unique', 'columns' => [$this->lastColumn()]]; return $this; }
    public function index(): self                       { $this->indexes[] = ['type' => 'index',  'columns' => [$this->lastColumn()]]; return $this; }
    public function primary(): self                     { return $this->modifyLast('primary',   true); }
    public function comment(string $text): self         { return $this->modifyLast('comment',   $text); }

    // ─── Çok-kolonlu index ────────────────────────────────────────
    public function uniqueIndex(array $columns, ?string $name = null): self
    {
        $this->indexes[] = ['type' => 'unique', 'columns' => $columns, 'name' => $name];
        return $this;
    }

    public function compositeIndex(array $columns, ?string $name = null): self
    {
        $this->indexes[] = ['type' => 'index', 'columns' => $columns, 'name' => $name];
        return $this;
    }

    // ─── SQL üretici ───────────────────────────────────────────────
    public function toSql(): string
    {
        return $this->driver === 'sqlite'
            ? $this->toSqliteSql()
            : $this->toMysqlSql();
    }

    protected function toMysqlSql(): string
    {
        $cols       = [];
        $primaryKey = null;

        foreach ($this->columns as $col) {
            $line = "`{$col['name']}` {$col['type']}";

            if (!empty($col['nullable']))         $line .= ' NULL';
            elseif (array_key_exists('nullable', $col) && $col['nullable'] === false) $line .= ' NOT NULL';

            if (array_key_exists('default', $col)) {
                $line .= ' DEFAULT ' . $this->formatDefault($col['default']);
            }
            if (!empty($col['on_update']))        $line .= ' ON UPDATE ' . $col['on_update'];
            if (!empty($col['auto_increment']))   $line .= ' AUTO_INCREMENT';
            if (!empty($col['comment']))          $line .= " COMMENT '" . str_replace("'", "''", $col['comment']) . "'";
            if (!empty($col['primary']))          $primaryKey = $col['name'];

            $cols[] = "  $line";
        }

        if ($primaryKey)  $cols[] = "  PRIMARY KEY (`$primaryKey`)";

        foreach ($this->indexes as $idx) {
            $colList = '`' . implode('`,`', $idx['columns']) . '`';
            $name    = $idx['name'] ?? ($this->table . '_' . implode('_', $idx['columns']) . '_' . ($idx['type'] === 'unique' ? 'unique' : 'index'));
            $cols[]  = $idx['type'] === 'unique'
                ? "  UNIQUE KEY `$name` ($colList)"
                : "  KEY `$name` ($colList)";
        }

        $body = implode(",\n", $cols);
        return "CREATE TABLE `{$this->table}` (\n$body\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collation};";
    }

    protected function toSqliteSql(): string
    {
        $cols       = [];
        $primaryKey = null;
        $hasAi      = false;

        foreach ($this->columns as $col) {
            // SQLite tip eşleme
            $type = $col['type'];
            if (!empty($col['auto_increment'])) {
                $type  = 'INTEGER';
                $hasAi = true;
            }
            $line = "\"{$col['name']}\" $type";

            if (!empty($col['primary']) && !empty($col['auto_increment'])) {
                $line .= ' PRIMARY KEY AUTOINCREMENT';
                $primaryKey = 'inline';
            } else {
                if (!empty($col['nullable'])) $line .= '';
                elseif (array_key_exists('nullable', $col) && $col['nullable'] === false) $line .= ' NOT NULL';
                if (array_key_exists('default', $col)) {
                    $line .= ' DEFAULT ' . $this->formatDefault($col['default']);
                }
                if (!empty($col['primary'])) $primaryKey = $col['name'];
            }
            $cols[] = "  $line";
        }

        if ($primaryKey && $primaryKey !== 'inline') {
            $cols[] = "  PRIMARY KEY (\"$primaryKey\")";
        }

        $body = implode(",\n", $cols);
        $sql  = "CREATE TABLE \"{$this->table}\" (\n$body\n);";

        // Index'leri ayrı statement'lar olarak
        foreach ($this->indexes as $idx) {
            $name    = $idx['name'] ?? ($this->table . '_' . implode('_', $idx['columns']) . '_' . ($idx['type'] === 'unique' ? 'unique' : 'idx'));
            $unique  = $idx['type'] === 'unique' ? 'UNIQUE ' : '';
            $colList = '"' . implode('","', $idx['columns']) . '"';
            $sql    .= "\nCREATE {$unique}INDEX \"$name\" ON \"{$this->table}\" ($colList);";
        }

        return $sql;
    }

    protected function formatDefault(mixed $value): string
    {
        if ($value === null) return 'NULL';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_int($value) || is_float($value)) return (string) $value;
        if (in_array($value, ['CURRENT_TIMESTAMP'], true)) return $value;
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected function addColumn(string $name, string $type, array $opts = []): self
    {
        $this->columns[] = array_merge(['name' => $name, 'type' => $type], $opts);
        return $this;
    }

    protected function modifyLast(string $key, mixed $value): self
    {
        if ($this->columns) {
            $this->columns[count($this->columns) - 1][$key] = $value;
        }
        return $this;
    }

    protected function lastColumn(): string
    {
        return $this->columns[count($this->columns) - 1]['name'] ?? '';
    }
}
