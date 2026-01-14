<?php
/**
 * Yapılandırma Yönetimi Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Config
{
    protected static array $config = [];
    protected static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) return;

        $files = glob(CONFIG_PATH . '/*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $data = require $file;
            if (is_array($data)) {
                self::$config[$key] = $data;
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        self::load();

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function set(string $key, $value): void
    {
        self::load();

        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k]) || !is_array($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
            }
        }
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    public static function all(): array
    {
        self::load();
        return self::$config;
    }

    public static function getGoogleApiKey(string $service): ?string
    {
        return self::get("google.{$service}_api_key");
    }

    public static function getMapsJsApiKey(): ?string
    {
        return self::get('google.maps_js_api_key');
    }

    public static function getMailConfig(): array
    {
        return self::get('mail', []);
    }

    public static function getAiConfig(): array
    {
        return self::get('ai', []);
    }

    public static function getDatabaseConfig(): array
    {
        return self::get('database', []);
    }
}
