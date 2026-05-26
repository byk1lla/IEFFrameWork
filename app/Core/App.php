<?php
/**
 * App — Framework bootstrap.
 *
 * Sırasıyla: config yükle → bakım kontrolü → session → lang → route → dispatch.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class App
{
    protected static ?App $instance = null;

    public function __construct()
    {
        self::$instance = $this;
        Config::load();
        DebugBar::getInstance(); // Debug bar başlat
    }

    public static function getInstance(): ?App
    {
        return self::$instance;
    }

    public function run(): void
    {
        // ─── Session + Dil ──────────────────────────────────────────
        Session::start();
        Lang::load();

        // ─── Bakım modu ─────────────────────────────────────────────
        if ($this->isUnderMaintenance()) {
            $this->serveMaintenancePage();
            return;
        }

        // ─── Router ─────────────────────────────────────────────────
        require CONFIG_PATH . '/routes.php';
        Router::dispatch();
    }

    /**
     * Bakım modu aktif mi? Admin login olanlar ve admin/login path'leri hariç.
     */
    protected function isUnderMaintenance(): bool
    {
        // Admin paneli ve auth route'ları her zaman erişilebilir (yoksa adminçıkamaz)
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, '/admin')) return false;
        if (str_starts_with($path, '/docs')) return false;
        if (in_array($path, ['/login', '/logout', '/sifre-sifirla'], true)) return false;
        if (str_starts_with($path, '/sifre-sifirla/')) return false;

        // Admin login olmuşsa bypass (siteyi normal görür)
        try {
            if (SiteContent::isAdminLoggedIn()) return false;
        } catch (\Throwable $e) { /* ignore */ }

        // Önce Setting (admin paneli), sonra config fallback
        try {
            $on = \App\Models\Setting::get('general.maintenance');
            if ($on !== null && $on !== '') return (bool) $on;
        } catch (\Throwable $e) { /* tablo yoksa atla */ }

        return (bool) Config::get('app.maintenance.enabled', false);
    }

    protected function serveMaintenancePage(): void
    {
        http_response_code(503);
        header('Retry-After: 3600');
        header('Content-Type: text/html; charset=utf-8');

        // Site adı + iletişim — Setting'ten
        $siteName = 'Site';
        $contactEmail = $contactPhone = $waNumber = '';
        $eta = $message = '';
        try {
            $general = \App\Models\Setting::group('general');
            $siteName     = $general['site_name']       ?? Config::get('app.name', 'Site');
            $contactEmail = $general['contact_email']   ?? '';
            $contactPhone = $general['contact_phone']   ?? '';
            $waNumber     = $general['whatsapp_number'] ?? '';
            $eta          = $general['maintenance_eta'] ?? '';
            $message      = $general['maintenance_message'] ?? '';
        } catch (\Throwable $e) { /* ignore */ }

        $view = APP_PATH . '/Views/errors/maintenance.php';
        if (file_exists($view)) {
            // değişkenler view'da kullanılabilir
            include $view;
        } else {
            echo '<h1>Sistem bakımda</h1>';
        }
    }
}
