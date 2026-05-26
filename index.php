<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════════
 *  🦅 IEF FRAMEWORK — Single Entry Point
 * ═══════════════════════════════════════════════════════════════════════════════
 *
 *  © IEF Software — https://iefsoftware.tr
 *
 *  Bu dosya gelen tüm isteklerin giriş noktasıdır. .htaccess (Apache) veya
 *  nginx try_files kuralı sayesinde public asset'ler hariç her istek burada
 *  son bulur. Ardından App::run() router'a devreder.
 * ═══════════════════════════════════════════════════════════════════════════════
 */

declare(strict_types=1);

// ─── Sabitler ────────────────────────────────────────────────────────────────
define('ROOT_PATH',    __DIR__);
define('APP_PATH',     ROOT_PATH . '/app');
define('CONFIG_PATH',  ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('ASSETS_PATH',  ROOT_PATH . '/assets');
define('VIEW_PATH',    APP_PATH  . '/Views');

// ─── PHP -S router için: public/ altındaki asset'leri manuel serve et ────────
// (public/ doc-root değil, bu yüzden return false işe yaramıyor — kendimiz okuyup yolluyoruz.)
if (PHP_SAPI === 'cli-server') {
    $_iefUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $_iefServe = null;
    foreach (['/assets/', '/uploads/', '/img/'] as $_iefDir) {
        if (str_starts_with($_iefUri, $_iefDir)) {
            $cand = PUBLIC_PATH . $_iefUri;
            if (is_file($cand)) { $_iefServe = $cand; break; }
        }
    }
    if ($_iefServe === null) {
        foreach (['/favicon.ico', '/robots.txt', '/sw.js'] as $_iefStatic) {
            if ($_iefUri === $_iefStatic && is_file(PUBLIC_PATH . $_iefStatic)) {
                $_iefServe = PUBLIC_PATH . $_iefStatic;
                break;
            }
        }
    }
    if ($_iefServe !== null) {
        $ext = strtolower(pathinfo($_iefServe, PATHINFO_EXTENSION));
        $mimes = [
            'css'=>'text/css', 'js'=>'application/javascript',
            'png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif',
            'webp'=>'image/webp','svg'=>'image/svg+xml','ico'=>'image/x-icon',
            'woff'=>'font/woff','woff2'=>'font/woff2','ttf'=>'font/ttf','eot'=>'application/vnd.ms-fontobject',
            'pdf'=>'application/pdf', 'json'=>'application/json',
            'mp4'=>'video/mp4', 'webm'=>'video/webm', 'mp3'=>'audio/mpeg',
            'txt'=>'text/plain', 'html'=>'text/html', 'xml'=>'application/xml',
        ];
        header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($_iefServe));
        header('Cache-Control: public, max-age=86400');
        readfile($_iefServe);
        exit;
    }
    unset($_iefUri, $_iefServe, $_iefDir, $cand, $_iefStatic);
}

// ─── Composer Autoload ───────────────────────────────────────────────────────
if (!file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    http_response_code(500);
    exit("IEF Framework: 'composer install' komutunu çalıştırın.\n");
}
require_once ROOT_PATH . '/vendor/autoload.php';

// ─── Config ön-yükle (encoding/timezone için lazım) ──────────────────────────
$appConfig = require CONFIG_PATH . '/app.php';

date_default_timezone_set($appConfig['timezone'] ?? 'Europe/Istanbul');
mb_internal_encoding('UTF-8');

// ─── Hata raporlama (debug moduna göre) ──────────────────────────────────────
$debug = (bool) ($appConfig['debug'] ?? false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');

// ─── Boot ────────────────────────────────────────────────────────────────────
use App\Core\App;
use App\Core\ExceptionHandler;

ExceptionHandler::setDebug($debug);

$_iefStart = microtime(true);

try {
    (new App())->run();
} catch (\Throwable $e) {
    ExceptionHandler::handle($e);
}

// ─── Analytics (response gönderildikten sonra arka planda) ────────────────────
if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
try {
    $svc = new \App\Services\AnalyticsService();
    $svc->record(
        $_SERVER['REQUEST_METHOD'] ?? 'GET',
        $_SERVER['REQUEST_URI']    ?? '/',
        http_response_code() ?: 200,
        (int) round((microtime(true) - $_iefStart) * 1000)
    );
} catch (\Throwable $e) {
    // sessiz — analytics ana akışı asla durdurmasın
}
