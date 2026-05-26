<?php
/**
 * Lang — basit i18n sistemi.
 *
 * resources/lang/<locale>.php dosyalarından yükler.
 * trans('welcome.title', ['name' => 'Efe']) → ':name' placeholder'ı replace olur.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Lang
{
    protected static string $locale = 'tr';
    protected static array  $translations = [];
    protected static bool   $loaded = false;

    public static function load(?string $locale = null): void
    {
        $locale = $locale
            ?? Session::get('locale')
            ?? Config::get('app.locale', 'tr');

        self::$locale = (string) $locale;
        $file = ROOT_PATH . '/resources/lang/' . self::$locale . '.php';

        if (file_exists($file)) {
            $data = require $file;
            self::$translations = is_array($data) ? $data : [];
        } else {
            // Fallback locale
            $fallback = (string) Config::get('app.fallback_locale', 'en');
            $fallbackFile = ROOT_PATH . '/resources/lang/' . $fallback . '.php';
            self::$translations = file_exists($fallbackFile) ? (array) require $fallbackFile : [];
        }

        self::$loaded = true;
    }

    public static function get(string $key, array $placeholders = []): string
    {
        if (!self::$loaded) self::load();

        $value = self::$translations;
        foreach (explode('.', $key) as $seg) {
            if (is_array($value) && array_key_exists($seg, $value)) {
                $value = $value[$seg];
            } else {
                $value = $key;
                break;
            }
        }
        if (!is_string($value)) $value = $key;

        foreach ($placeholders as $k => $v) {
            $value = str_replace(':' . $k, (string) $v, $value);
        }
        return $value;
    }

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        Session::set('locale', $locale);
        self::$loaded = false;
        self::load();
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }
}
