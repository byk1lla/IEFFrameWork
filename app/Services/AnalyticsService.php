<?php
/**
 * AnalyticsService — trafik loglama: HTTP request, tekil ziyaretçi, bot tespiti.
 *
 * Onursurucukursu projesindeki sistemin baştan aşağı portu.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;

class AnalyticsService
{
    /** Bot user-agent imzaları */
    protected const BOT_PATTERNS = [
        'bot','crawl','spider','slurp','curl','wget','python-requests','httpclient',
        'headless','phantomjs','selenium','playwright','puppeteer','lighthouse',
        'facebookexternalhit','whatsapp','telegrambot','twitterbot','linkedinbot',
        'googlebot','bingbot','yandex','baiduspider','ahrefsbot','semrushbot','mj12bot',
        'duckduckbot','applebot','petalbot','dotbot','rogerbot','ia_archiver','archive.org_bot',
    ];

    /** Aday yollar (gerçek kullanıcı erişmez) — bot işareti */
    protected const BOT_PATH_TRAPS = ['/wp-admin','/wp-login','/.env','/phpmyadmin','/.git/'];

    /**
     * Request'i kayıt et. Asset / admin / olay-callback yollarını atlar.
     * @return array{session_id:int,is_bot:bool}|null Atlanmışsa null
     */
    public function record(string $method, string $uri, int $statusCode = 200, int $responseMs = 0): ?array
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        if ($this->isIgnorablePath($path)) return null;

        $db = Database::getInstance();
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
        $ip = $this->clientIp();
        $referrer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);

        $fingerprint = $this->fingerprint($ip, $ua);
        $session = $this->getOrCreateSession($fingerprint, $ip, $ua, $referrer);

        $isBot = (bool) $session['is_bot'] || $this->detectBotByPath($path);
        if ($this->detectBotByPath($path) && !$session['is_bot']) {
            $db->execute(
                "UPDATE traffic_sessions SET is_bot=1, bot_reason='path-trap' WHERE id=?",
                [$session['id']]
            );
        }

        // Log
        $db->execute(
            "INSERT INTO traffic_logs (session_id, method, path, query_string, status_code, response_ms, referrer, is_bot)
             VALUES (?,?,?,?,?,?,?,?)",
            [
                (int) $session['id'],
                substr($method, 0, 10),
                substr($path, 0, 500),
                substr(parse_url($uri, PHP_URL_QUERY) ?: '', 0, 500),
                $statusCode,
                max(0, $responseMs),
                $referrer ?: null,
                $isBot ? 1 : 0,
            ]
        );

        // Session counters
        $db->execute(
            "UPDATE traffic_sessions SET last_seen_at=NOW(), request_count = request_count + 1 WHERE id=?",
            [(int) $session['id']]
        );

        // Auth giriş yapmışsa session'a bağla
        $userId = Auth::id();
        if ($userId && (int) ($session['user_id'] ?? 0) !== (int) $userId) {
            $db->execute("UPDATE traffic_sessions SET user_id=? WHERE id=?", [(int) $userId, (int) $session['id']]);
        }

        return ['session_id' => (int) $session['id'], 'is_bot' => $isBot];
    }

    /**
     * Frontend'den gelen etkileşim olayını kaydet.
     */
    public function recordEvent(int $sessionId, string $eventType, string $path = '', string $target = '', string $value = ''): void
    {
        $allowed = ['page_view','click','scroll','form_submit','form_focus','video_play','video_pause','copy','outbound','search'];
        if (!in_array($eventType, $allowed, true)) return;

        Database::getInstance()->execute(
            "INSERT INTO traffic_events (session_id, event_type, path, target, value) VALUES (?,?,?,?,?)",
            [$sessionId, $eventType, substr($path, 0, 500), substr($target, 0, 300), substr($value, 0, 500)]
        );
    }

    /**
     * Bir IP+UA'nın session'ını bul ya da oluştur.
     */
    public function getOrCreateSession(string $fingerprint, string $ip, string $ua, string $referrer = ''): array
    {
        $db = Database::getInstance();
        $row = $db->fetch("SELECT * FROM traffic_sessions WHERE fingerprint=? LIMIT 1", [$fingerprint]);
        if ($row) return $row;

        $parsed = $this->parseUserAgent($ua);
        $isBot = $this->detectBotByUserAgent($ua);
        $refHost = $referrer ? (parse_url($referrer, PHP_URL_HOST) ?: '') : '';

        $db->execute(
            "INSERT INTO traffic_sessions
             (fingerprint, ip, user_agent, is_bot, bot_reason, device, browser, os, referrer_host, utm_source, utm_medium, utm_campaign)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $fingerprint, $ip, $ua,
                $isBot ? 1 : 0, $isBot ? 'user-agent' : null,
                $parsed['device'], $parsed['browser'], $parsed['os'],
                $refHost ?: null,
                substr($_GET['utm_source']   ?? '', 0, 80) ?: null,
                substr($_GET['utm_medium']   ?? '', 0, 80) ?: null,
                substr($_GET['utm_campaign'] ?? '', 0, 120) ?: null,
            ]
        );

        return $db->fetch("SELECT * FROM traffic_sessions WHERE fingerprint=? LIMIT 1", [$fingerprint]);
    }

    public function fingerprint(string $ip, string $ua): string
    {
        $salt = (string) Config::get('app.key', 'ief-framework-default-salt');
        return hash('sha256', $ip . '|' . $ua . '|' . $salt);
    }

    public function clientIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) {
                $val = $_SERVER[$k];
                if (str_contains($val, ',')) $val = trim(explode(',', $val)[0]);
                if (filter_var($val, FILTER_VALIDATE_IP)) return $val;
            }
        }
        return '0.0.0.0';
    }

    // ─── Internal ────────────────────────────────────────────────
    protected function detectBotByUserAgent(string $ua): bool
    {
        if ($ua === '') return true;
        $ua = strtolower($ua);
        foreach (self::BOT_PATTERNS as $p) {
            if (str_contains($ua, $p)) return true;
        }
        return false;
    }

    protected function detectBotByPath(string $path): bool
    {
        foreach (self::BOT_PATH_TRAPS as $p) {
            if (str_starts_with($path, $p)) return true;
        }
        return false;
    }

    protected function isIgnorablePath(string $path): bool
    {
        if (str_starts_with($path, '/assets/'))             return true;
        if (str_starts_with($path, '/uploads/'))            return true;
        if (str_starts_with($path, '/admin/analytics/event')) return true;
        if (preg_match('/\.(css|js|png|jpe?g|gif|webp|svg|woff2?|ttf|ico|map|mp4|webm|pdf)$/i', $path)) return true;
        return false;
    }

    protected function parseUserAgent(string $ua): array
    {
        $ual = strtolower($ua);
        $device = 'desktop';
        if (preg_match('/mobile|iphone|android.*mobile|windows phone/i', $ual)) $device = 'mobile';
        elseif (preg_match('/ipad|tablet|playbook/i', $ual)) $device = 'tablet';

        $browser = 'Other';
        if      (str_contains($ual, 'edg/'))                                       $browser = 'Edge';
        elseif  (str_contains($ual, 'opr/') || str_contains($ual, 'opera'))        $browser = 'Opera';
        elseif  (str_contains($ual, 'firefox/'))                                   $browser = 'Firefox';
        elseif  (str_contains($ual, 'chrome/') && !str_contains($ual, 'chromium'))$browser = 'Chrome';
        elseif  (str_contains($ual, 'safari/'))                                    $browser = 'Safari';

        $os = 'Other';
        if      (str_contains($ual, 'windows nt'))   $os = 'Windows';
        elseif  (str_contains($ual, 'mac os x'))     $os = 'macOS';
        elseif  (str_contains($ual, 'android'))      $os = 'Android';
        elseif  (preg_match('/iphone|ipad|ipod/i', $ual)) $os = 'iOS';
        elseif  (str_contains($ual, 'linux'))        $os = 'Linux';

        return ['device' => $device, 'browser' => $browser, 'os' => $os];
    }
}
