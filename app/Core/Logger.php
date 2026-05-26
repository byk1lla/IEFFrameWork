<?php
/**
 * Logger — basit dosya tabanlı log sistemi.
 *
 * Loglar storage/logs/ief-YYYY-MM-DD.log dosyasına düşer.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Logger
{
    protected static string $logPath = STORAGE_PATH . '/logs';

    public static function info(string $message, array $context = []): void    { self::write('INFO',    $message, $context); }
    public static function warning(string $message, array $context = []): void { self::write('WARNING', $message, $context); }
    public static function error(string $message, array $context = []): void   { self::write('ERROR',   $message, $context); }

    public static function debug(string $message, array $context = []): void
    {
        if (ExceptionHandler::isDebug()) {
            self::write('DEBUG', $message, $context);
        }
    }

    protected static function write(string $level, string $message, array $context = []): void
    {
        if (!is_dir(self::$logPath)) {
            @mkdir(self::$logPath, 0775, true);
        }

        $ctx   = $context ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) : '';
        $entry = sprintf("[%s] [%s] %s%s\n", date('Y-m-d H:i:s'), $level, $message, $ctx);
        $file  = self::$logPath . '/ief-' . date('Y-m-d') . '.log';

        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }
}
