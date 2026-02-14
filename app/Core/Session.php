<?php
/**
 * Oturum Yönetimi Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Session
{
    protected static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $config = require CONFIG_PATH . '/app.php';
        $sessionConfig = $config['session'] ?? [];

        session_name($sessionConfig['name'] ?? 'TAVUK_SESSION');

        session_set_cookie_params([
            'lifetime' => ($sessionConfig['lifetime'] ?? 120) * 60,
            'path' => $sessionConfig['path'] ?? '/',
            'domain' => $sessionConfig['domain'] ?? '',
            'secure' => $sessionConfig['secure'] ?? false,
            'httponly' => $sessionConfig['httponly'] ?? true,
            'samesite' => $sessionConfig['samesite'] ?? 'Lax',
        ]);

        // Session dosyalarını proje içinde sakla
        $sessionPath = ROOT_PATH . '/sessions';
        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0777, true);
        }
        session_save_path($sessionPath);

        session_start();
        self::$started = true;

        // CSRF token oluştur
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function clear(): void
    {
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::clear();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function getCsrfToken(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (!$token)
            return false;
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function getAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function userId(): ?string
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function userRole(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }
}
