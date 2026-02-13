<?php

namespace App\Core;

/**
 * Structured Logger for IEF Framework
 */
class Logger
{
    private static string $logPath = STORAGE_PATH . '/logs';

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        if (ExceptionHandler::isDebug()) {
            self::log('DEBUG', $message, $context);
        }
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    private static function log(string $level, string $message, array $context = []): void
    {
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }

        $date = date('Y-m-d');
        $time = date('H:i:s');
        $file = self::$logPath . "/ief-{$date}.log";

        $contextString = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $entry = "[{$time}] [{$level}] {$message}{$contextString}\n";

        file_put_contents($file, $entry, FILE_APPEND);
    }
}
