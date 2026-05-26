<?php
/**
 * Session — Güvenli session ve CSRF yönetimi.
 *
 * - Session dosyaları proje içindeki sessions/ klasöründe tutulur.
 * - Otomatik CSRF token üretir.
 * - Flash data desteği (_flash anahtarı altında).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Session
{
    protected static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::ensureCsrf();
            return;
        }

        $appCfg  = Config::get('app', []);
        $sessCfg = $appCfg['session'] ?? [];
        $secure  = ($appCfg['env'] ?? 'development') === 'production';

        session_name($sessCfg['name'] ?? 'IEF_SESSION');

        session_set_cookie_params([
            'lifetime' => (int) ($sessCfg['lifetime'] ?? 120) * 60,
            'path'     => $sessCfg['path']     ?? '/',
            'domain'   => $sessCfg['domain']   ?? '',
            'secure'   => $sessCfg['secure']   ?? $secure,
            'httponly' => $sessCfg['httponly'] ?? true,
            'samesite' => $sessCfg['samesite'] ?? 'Lax',
        ]);

        // Güvenlik: sessions storage/ altında, web root erişimi yok.
        $path = STORAGE_PATH . '/sessions';
        if (!is_dir($path)) mkdir($path, 0700, true);
        @chmod($path, 0700);
        session_save_path($path);

        session_start();
        self::$started = true;
        self::ensureCsrf();
    }

    protected static function ensureCsrf(): void
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // ─── Temel get/set ───────────────────────────────────────────────
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
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

    public static function all(): array
    {
        return $_SESSION ?? [];
    }

    public static function clear(): void
    {
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::clear();
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::$started = false;
    }

    public static function regenerate(bool $deleteOld = true): void
    {
        session_regenerate_id($deleteOld);
        self::ensureCsrf();
    }

    // ─── CSRF ────────────────────────────────────────────────────────
    public static function csrfToken(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }

    /** Alias (helper geriye dönük) */
    public static function getCsrfToken(): string
    {
        return self::csrfToken();
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (!$token) return false;
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }

    // ─── Flash data ──────────────────────────────────────────────────
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function allFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }

    // ─── Auth kısayolları (Auth ile uyumlu) ──────────────────────────
    public static function user(): ?array
    {
        return $_SESSION['auth_user'] ?? null;
    }

    public static function userId(): mixed
    {
        return $_SESSION['auth_user_id'] ?? null;
    }

    public static function userRole(): ?string
    {
        return $_SESSION['auth_user']['role'] ?? null;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['auth_user_id']);
    }
}
