<?php
/**
 * SiteContent — Canlı (inline) editör için içerik repository + edit mode kontrolü.
 * Onur Sürücü Kursu projesinden birebir port.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class SiteContent
{
    /** @var array<string,string> */
    protected static array $cache = [];
    protected static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) return;
        try {
            $rows = Database::getInstance()->fetchAll("SELECT content_key, value FROM site_content");
            foreach ($rows as $r) {
                self::$cache[$r['content_key']] = (string)($r['value'] ?? '');
            }
        } catch (\Throwable $e) {
            // tablo yoksa sessizce geç (migrate öncesi vb.)
        }
        self::$loaded = true;
    }

    public static function get(string $key, string $default = ''): string
    {
        self::load();
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, string $value, string $type = 'text', ?string $page = null, ?string $section = null, ?string $label = null): bool
    {
        $db = Database::getInstance();
        $existing = $db->fetch("SELECT id FROM site_content WHERE content_key = ?", [$key]);

        if ($existing) {
            $db->execute("UPDATE site_content SET value = ?, content_type = ? WHERE content_key = ?", [$value, $type, $key]);
        } else {
            $db->execute(
                "INSERT INTO site_content (content_key, content_type, value, page, section, label) VALUES (?,?,?,?,?,?)",
                [$key, $type, $value, $page, $section, $label]
            );
        }
        self::$cache[$key] = $value;
        return true;
    }

    /**
     * Edit mode aktif mi? Session'da editing=true + admin login.
     * (Geri uyum: ?edit=1 query parametresi de aktif eder.)
     */
    public static function isEditing(): bool
    {
        if (!self::isAdminLoggedIn()) return false;
        if (Session::get('site_editor.active') === true) return true;
        if (isset($_GET['edit']) && $_GET['edit'] === '1') return true;
        return false;
    }

    /**
     * Edit modunu session'a yaz.
     */
    public static function startEditing(): void
    {
        Session::set('site_editor.active', true);
    }

    /**
     * Edit modunu kapat.
     */
    public static function stopEditing(): void
    {
        Session::set('site_editor.active', false);
    }

    /**
     * Aktif sayfanın gövdesi editlenebilir mi?
     * Blog post/list sayfaları HARİÇ — onlar kendi admin'inden yönetiliyor.
     */
    public static function isPageEditable(): bool
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, '/blog')) return false;
        return true;
    }

    /**
     * Verilen path için kayıtlı auto-edit override'lerini döner.
     * Key format: auto.{path}.{xpath}
     * @return array<string,string> xpath => value
     */
    public static function getAutoOverrides(string $path): array
    {
        try {
            $prefix = 'auto.' . self::normalizePath($path) . '.';
            $rows = Database::getInstance()->fetchAll(
                "SELECT content_key, value FROM site_content WHERE content_key LIKE ?",
                [$prefix . '%']
            );
            $out = [];
            $prefixLen = strlen($prefix);
            foreach ($rows as $r) {
                $xpath = substr($r['content_key'], $prefixLen);
                $out[$xpath] = (string)($r['value'] ?? '');
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function normalizePath(string $path): string
    {
        $p = trim($path, '/');
        if ($p === '') $p = 'home';
        $p = preg_replace('/[^a-z0-9-]/i', '-', $p);
        return strtolower($p);
    }

    public static function isAdminLoggedIn(): bool
    {
        $user = Session::get('auth_user') ?? Session::get('user');
        if (!$user) return false;
        $role = is_array($user) ? ($user['role'] ?? '') : ($user->role ?? '');
        return in_array($role, ['superadmin', 'admin', 'super_admin', 'editor'], true);
    }
}
