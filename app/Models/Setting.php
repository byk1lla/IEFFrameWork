<?php
/**
 * Setting — KV store. Setting::get('app.name'), Setting::set(...).
 *
 * Not: Bilinçli olarak Model'den extend ETMİYOR (statik facade ve instance API'leri
 * çakışır). Doğrudan Database'i kullanır.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Setting
{
    /** Request scope cache */
    protected static array $cache = [];
    protected static bool  $loaded = false;

    /**
     * Tek anahtar oku: Setting::get('general.site_name', 'default')
     */
    public static function get(string $dotKey, mixed $default = null): mixed
    {
        self::loadAll();
        return self::$cache[$dotKey] ?? $default;
    }

    public static function set(string $dotKey, mixed $value, string $type = 'string'): void
    {
        [$group, $key] = self::splitKey($dotKey);
        $db = Database::getInstance();

        $row = $db->fetch("SELECT id FROM settings WHERE `group` = ? AND `key` = ? LIMIT 1", [$group, $key]);
        $encoded = self::encode($value, $type);

        if ($row) {
            $db->execute(
                "UPDATE settings SET `value` = ?, `type` = ?, updated_at = ? WHERE id = ?",
                [$encoded, $type, date('Y-m-d H:i:s'), $row['id']]
            );
        } else {
            $now = date('Y-m-d H:i:s');
            $db->execute(
                "INSERT INTO settings (`group`, `key`, `value`, `type`, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)",
                [$group, $key, $encoded, $type, $now, $now]
            );
        }
        self::$cache[$dotKey] = self::decode($encoded, $type);
    }

    /**
     * Belirli grubun tüm key/value'sını dict olarak döndür.
     */
    public static function group(string $group): array
    {
        self::loadAll();
        $out = [];
        foreach (self::$cache as $dot => $val) {
            if (str_starts_with($dot, $group . '.')) {
                $out[substr($dot, strlen($group) + 1)] = $val;
            }
        }
        return $out;
    }

    public static function all(): array
    {
        self::loadAll();
        return self::$cache;
    }

    public static function forget(string $dotKey): void
    {
        [$group, $key] = self::splitKey($dotKey);
        Database::getInstance()->execute(
            "DELETE FROM settings WHERE `group` = ? AND `key` = ?",
            [$group, $key]
        );
        unset(self::$cache[$dotKey]);
    }

    public static function refresh(): void
    {
        self::$cache  = [];
        self::$loaded = false;
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected static function loadAll(): void
    {
        if (self::$loaded) return;
        try {
            $rows = Database::getInstance()->fetchAll("SELECT `group`, `key`, `value`, `type` FROM settings");
            foreach ($rows as $r) {
                self::$cache[$r['group'] . '.' . $r['key']] = self::decode($r['value'], $r['type']);
            }
        } catch (\Throwable) {
            // settings tablosu henüz yoksa sessizce geç
        }
        self::$loaded = true;
    }

    protected static function splitKey(string $dot): array
    {
        if (str_contains($dot, '.')) {
            [$g, $k] = explode('.', $dot, 2);
            return [$g, $k];
        }
        return ['general', $dot];
    }

    protected static function encode(mixed $value, string $type): ?string
    {
        return match ($type) {
            'bool' => $value ? '1' : '0',
            'int'  => (string) (int) $value,
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE),
            default => $value === null ? null : (string) $value,
        };
    }

    protected static function decode(?string $raw, string $type): mixed
    {
        if ($raw === null) return null;
        return match ($type) {
            'bool' => $raw === '1' || $raw === 'true',
            'int'  => (int) $raw,
            'json' => json_decode($raw, true),
            default => $raw,
        };
    }
}
