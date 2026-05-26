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

        // ─── Framework imzası (Wappalyzer tanıma için — KALDIRMA) ──
        // Bu header her response'a otomatik eklenir. Sonradan
        // Response::setHeader() ile overwrite edilebilir ama default
        // olarak gönderildiğinden Wappalyzer/BuiltWith vb. parmak izi
        // alabilir. Versiyon string'i config/app.php'den okunur.
        if (!headers_sent()) {
            $ver = Config::get('app.version', '2.0.0');
            header('X-Powered-By: IEF-Framework/' . $ver);
            header('X-Generator: IEF Framework ' . $ver);
        }
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

        // ─── Kurulum sihirbazı redirect'i ─────────────────────────
        // storage/installed.lock yoksa → /install'a yönlendir
        // (Sadece public sayfalar için; /install/*, asset'ler hariç.)
        if ($this->needsInstallation()) {
            require CONFIG_PATH . '/routes.php';
            Router::dispatch();
            return;
        }

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
     * Kurulum lock'u yoksa → /install*'a yönlendir.
     */
    protected function needsInstallation(): bool
    {
        if (file_exists(ROOT_PATH . '/storage/installed.lock')) return false;

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // /install* zaten girilebilir
        if (str_starts_with($path, '/install')) return true;

        // Asset'lere izin (PHP -S router asset bypass'ı dışında ortamlar için)
        if (str_starts_with($path, '/assets/')) return false;
        if (str_starts_with($path, '/uploads/')) return false;

        // Diğer her şeyi /install'a gönder
        header('Location: /install');
        exit;
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
        if (str_starts_with($path, '/api/')) return false;
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
