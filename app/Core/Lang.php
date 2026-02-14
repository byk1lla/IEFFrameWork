<?php

namespace App\Core;

class Lang
{
    protected static string $locale = 'tr';
    protected static array $translations = [];

    public static function load(): void
    {
        self::$locale = Session::get('locale', 'tr');
        $file = ROOT_PATH . "/resources/lang/" . self::$locale . ".php";

        if (file_exists($file)) {
            self::$translations = require $file;
        }
    }

    public static function get(string $key, array $placeholders = []): string
    {
        $value = self::$translations[$key] ?? $key;

        foreach ($placeholders as $k => $v) {
            $value = str_replace(':' . $k, $v, $value);
        }

        return $value;
    }

    public static function setLocale(string $locale): void
    {
        if (in_array($locale, ['tr', 'en'])) {
            self::$locale = $locale;
            Session::set('locale', $locale);
            self::load();
        }
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }
}
