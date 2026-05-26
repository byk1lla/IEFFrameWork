<?php
/**
 * MigrationRunner — CLI tarafından kullanılan migration motor.
 *
 * Komutlar (CLI):
 *   ./ief migrate            → bekleyenleri çalıştır
 *   ./ief migrate:rollback   → son N migration'ı geri al
 *   ./ief migrate:fresh      → tüm tabloları DROP + sıfırdan migrate
 *   ./ief migrate:status     → liste
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class MigrationRunner
{
    protected Database $db;
    protected string   $table;
    protected string   $path;

    public function __construct()
    {
        $this->db    = Database::getInstance();
        $this->table = (string) Config::get('database.migrations_table', 'migrations');
        $this->path  = APP_PATH . '/Database/Migrations';
        $this->ensureTable();
    }

    protected function ensureTable(): void
    {
        if (Schema::hasTable($this->table)) return;

        Schema::create($this->table, function (Blueprint $t) {
            $t->id();
            $t->string('migration', 255);
            $t->integer('batch');
            $t->timestamp('executed_at')->default('CURRENT_TIMESTAMP');
        });
    }

    public function migrate(): void
    {
        $applied = $this->appliedSet();
        $files   = glob($this->path . '/*.php') ?: [];
        sort($files);

        $batch   = $this->currentBatch() + 1;
        $count   = 0;

        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (isset($applied[$name])) continue;

            $this->log("→ {$name}", 'cyan');
            require_once $file;
            $class = $this->resolveClass($name);
            $inst  = new $class();
            $inst->up($this->db);

            $this->db->execute(
                "INSERT INTO `{$this->table}` (migration, batch) VALUES (?, ?)",
                [$name, $batch]
            );
            $this->log("  ✓ migrated", 'green');
            $count++;
        }

        $count
            ? $this->log("Toplam {$count} migration çalıştırıldı (batch #{$batch}).", 'green')
            : $this->log("Çalıştırılacak migration yok.", 'yellow');
    }

    public function rollback(int $steps = 1): void
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` ORDER BY batch DESC, id DESC LIMIT ?",
            [$steps]
        );
        if (!$rows) { $this->log("Geri alınacak migration yok.", 'yellow'); return; }

        foreach ($rows as $row) {
            $name = $row['migration'];
            $file = $this->path . "/{$name}.php";
            if (!file_exists($file)) {
                $this->log("⚠ Dosya yok, atlanıyor: {$name}", 'yellow');
                continue;
            }
            $this->log("← {$name}", 'cyan');
            require_once $file;
            $class = $this->resolveClass($name);
            (new $class())->down($this->db);
            $this->db->execute("DELETE FROM `{$this->table}` WHERE id = ?", [$row['id']]);
            $this->log("  ✓ rolled back", 'green');
        }
    }

    public function fresh(): void
    {
        $this->log("Tüm tablolar DROP ediliyor...", 'yellow');
        $tables = $this->listTables();
        foreach ($tables as $t) {
            $this->db->exec("DROP TABLE IF EXISTS `$t`");
            $this->log("  - drop $t");
        }
        $this->ensureTable();
        $this->migrate();
    }

    public function status(): void
    {
        $applied = $this->appliedSet();
        $files   = glob($this->path . '/*.php') ?: [];
        sort($files);

        $this->log(sprintf("%-50s %-8s %s", 'MIGRATION', 'BATCH', 'STATUS'), 'bold');
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (isset($applied[$name])) {
                $row = $applied[$name];
                $this->log(sprintf("%-50s %-8s %s", $name, '#' . $row['batch'], 'ran'), 'green');
            } else {
                $this->log(sprintf("%-50s %-8s %s", $name, '-', 'pending'), 'yellow');
            }
        }
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected function appliedSet(): array
    {
        $rows = $this->db->fetchAll("SELECT migration, batch FROM `{$this->table}`");
        $set  = [];
        foreach ($rows as $r) $set[$r['migration']] = $r;
        return $set;
    }

    protected function currentBatch(): int
    {
        $row = $this->db->fetch("SELECT MAX(batch) AS b FROM `{$this->table}`");
        return (int) ($row['b'] ?? 0);
    }

    protected function listTables(): array
    {
        $driver = $this->db->getDriver();
        if ($driver === 'sqlite') {
            $rows = $this->db->fetchAll("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            return array_column($rows, 'name');
        }
        $rows = $this->db->fetchAll("SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
        return array_column($rows, 'name');
    }

    protected function resolveClass(string $name): string
    {
        return 'App\\Database\\Migrations\\' . $name;
    }

    protected function log(string $msg, string $color = 'reset'): void
    {
        $codes = ['reset' => 0, 'bold' => 1, 'red' => 31, 'green' => 32, 'yellow' => 33, 'cyan' => 36];
        $c     = $codes[$color] ?? 0;
        echo "\033[{$c}m{$msg}\033[0m\n";
    }
}
