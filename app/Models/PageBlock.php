<?php
/**
 * PageBlock — site editör bloğu (KV: page.block_key → content).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class PageBlock extends Model
{
    protected static string $table = 'page_blocks';
    protected static array  $fillable = ['page', 'block_key', 'content', 'type', 'label'];

    /** Request scope cache */
    protected static array $cache = [];
    protected static bool  $loaded = false;

    public static function value(string $page, string $key, ?string $default = null): ?string
    {
        self::loadAll();
        return self::$cache["$page.$key"] ?? $default;
    }

    public static function set(string $page, string $key, ?string $content, string $type = 'text', ?string $label = null): void
    {
        $db = Database::getInstance();
        $row = $db->fetch("SELECT id FROM page_blocks WHERE page=? AND block_key=? LIMIT 1", [$page, $key]);
        $now = date('Y-m-d H:i:s');

        if ($row) {
            $db->execute(
                "UPDATE page_blocks SET content=?, type=?, label=COALESCE(?, label), updated_at=? WHERE id=?",
                [$content, $type, $label, $now, $row['id']]
            );
        } else {
            $db->execute(
                "INSERT INTO page_blocks (page, block_key, content, type, label, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$page, $key, $content, $type, $label, $now, $now]
            );
        }
        self::$cache["$page.$key"] = $content;
    }

    public static function allByPage(string $page): array
    {
        $rows = Database::getInstance()->fetchAll(
            "SELECT * FROM page_blocks WHERE page=? ORDER BY block_key ASC",
            [$page]
        );
        return $rows;
    }

    public static function refresh(): void
    {
        self::$cache = [];
        self::$loaded = false;
    }

    /**
     * Bir path için auto-scanner override'larını döndür.
     * Key format: auto.{normalizedPath}.{xpath} → value
     */
    public static function autoOverridesForPath(string $path): array
    {
        $norm   = self::normalizePath($path);
        $prefix = 'auto.' . $norm . '.';
        try {
            $rows = Database::getInstance()->fetchAll(
                "SELECT block_key, content, type FROM page_blocks WHERE page = ? AND block_key LIKE ?",
                ['auto:' . $norm, '%']
            );
            // Alternatif olarak `page = "auto"` ve block_key prefix'i
            if (!$rows) {
                $rows = Database::getInstance()->fetchAll(
                    "SELECT CONCAT(page, '.', block_key) AS dot, content, type
                     FROM page_blocks
                     WHERE page = ? AND block_key LIKE ?",
                    ['auto.' . $norm, '%']
                );
                $out = [];
                foreach ($rows as $r) {
                    // dot = 'auto.<path>.{xpath}' → extract xpath
                    $key = substr($r['dot'], strlen($prefix));
                    $out[$key] = ['v' => $r['content'], 't' => $r['type']];
                }
                return $out;
            }
        } catch (\Throwable) {}
        return [];
    }

    /** URL path'i normalize et: '/' → 'home', '/blog/x' → 'blog-x' */
    public static function normalizePath(string $path): string
    {
        $p = trim($path, '/');
        if ($p === '') $p = 'home';
        $p = preg_replace('/[^a-z0-9-]/i', '-', $p);
        return strtolower($p);
    }

    protected static function loadAll(): void
    {
        if (self::$loaded) return;
        try {
            $rows = Database::getInstance()->fetchAll("SELECT page, block_key, content FROM page_blocks");
            foreach ($rows as $r) self::$cache[$r['page'] . '.' . $r['block_key']] = $r['content'];
        } catch (\Throwable) { /* tablo yoksa sessizce geç */ }
        self::$loaded = true;
    }
}
