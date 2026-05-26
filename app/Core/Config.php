<?php
/**
 * Config — config/ klasöründeki PHP dosyalarını yükler.
 *
 * Dot-notation desteği: Config::get('app.name'), Config::get('database.driver')
 * .env KULLANILMAZ — tüm ayarlar config/*.php içinde.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Config
{
    /** @var array<string,mixed> */
    protected static array $items  = [];
    protected static bool  $loaded = false;

    /**
     * config/ altındaki tüm *.php dosyalarını yükle (routes.php hariç).
     */
    public static function load(): void
    {
        if (self::$loaded) return;

        foreach (glob(CONFIG_PATH . '/*.php') ?: [] as $file) {
            $name = basename($file, '.php');
            if ($name === 'routes') continue; // Router require eder
            $data = require $file;
            if (is_array($data)) {
                self::$items[$name] = $data;
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) self::load();

        $value = self::$items;
        foreach (explode('.', $key) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        if (!self::$loaded) self::load();

        $segments = explode('.', $key);
        $ref      = &self::$items;
        foreach ($segments as $i => $seg) {
            if ($i === count($segments) - 1) {
                $ref[$seg] = $value;
                return;
            }
            if (!isset($ref[$seg]) || !is_array($ref[$seg])) {
                $ref[$seg] = [];
            }
            $ref = &$ref[$seg];
        }
    }

    public static function has(string $key): bool
    {
        return self::get($key, '__NOT_FOUND__') !== '__NOT_FOUND__';
    }

    public static function all(): array
    {
        if (!self::$loaded) self::load();
        return self::$items;
    }

    public static function reset(): void
    {
        self::$items  = [];
        self::$loaded = false;
    }
}
