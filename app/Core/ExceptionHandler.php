<?php
/**
 * Exception Handler - Laravel Benzeri Hata Sayfası
 * 
 * @package    IEF Framework
 */

namespace App\Core;

use Throwable;

class ExceptionHandler
{
    /**
     * Debug modu aktif mi
     */
    protected static bool $debug = true;

    /**
     * Exception'ı işle
     */
    public static function handle(Throwable $e): void
    {
        // Log'a yaz
        self::logException($e);

        // HTTP status code
        $statusCode = self::getStatusCode($e);

        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        // AJAX/JSON istekleri için JSON hata döndür
        if (self::isAjaxRequest()) {
            self::renderJsonError($e, $statusCode);
            return;
        }

        // Debug modunda detaylı hata göster
        if (self::$debug) {
            self::renderDebugPage($e);
        } else {
            self::renderProductionPage($statusCode);
        }
    }

    /**
     * AJAX isteği mi kontrol et
     */
    protected static function isAjaxRequest(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
    }

    /**
     * JSON hata yanıtı döndür
     */
    protected static function renderJsonError(Throwable $e, int $statusCode): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        $response = [
            'success' => false,
            'error' => self::$debug ? $e->getMessage() : 'Bir hata oluştu',
        ];

        if (self::$debug) {
            $response['exception'] = get_class($e);
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Debug modunu ayarla
     */
    public static function setDebug(bool $debug): void
    {
        self::$debug = $debug;
    }

    public static function isDebug(): bool
    {
        return self::$debug;
    }

    /**
     * Exception'ı logla
     */
    protected static function logException(Throwable $e): void
    {
        $logFile = dirname(__DIR__, 2) . '/storage/logs/error-' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        error_log($message, 3, $logFile);
    }

    /**
     * HTTP status code belirle
     */
    protected static function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return (int) $e->getStatusCode();
        }

        $code = $e->getCode();

        if (is_int($code) && $code >= 400 && $code < 600) {
            return $code;
        }

        return 500;
    }

    /**
     * Debug sayfası render et (Laravel benzeri)
     */
    protected static function renderDebugPage(Throwable $e): void
    {
        $class = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTrace();

        // Kod snippet'ı al
        $codeSnippet = self::getCodeSnippet($file, $line);

        // Stack trace HTML oluştur
        $traceHtml = self::buildTraceHtml($trace);

        // Request bilgileri
        $requestInfo = self::getRequestInfo();

        // Server bilgileri
        $serverInfo = self::getServerInfo();

        // JS için JSON veriler
        $traceJson = json_encode($trace);
        $requestJson = json_encode(self::collectRequestData());
        $serverJson = json_encode(self::collectServerData());

        // String verileri de JSON encode et (güvenlik için)
        $classJson = json_encode($class);
        $messageJson = json_encode($message);
        $fileJson = json_encode($file);

        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hata - IEF Framework</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #020617;
            --card-bg: rgba(15, 23, 42, 0.7);
            --danger: #ef4444;
            --text: #f3f4f6;
            --muted: #9ca3af;
            --border: rgba(255, 255, 255, 0.1);
            --accent: #00D1FF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            background-image: radial-gradient(circle at top right, rgba(239, 68, 68, 0.05), transparent 40%);
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        
        .header {
            background: var(--danger);
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 30px;
            box-shadow: 0 20px 50px rgba(239, 68, 68, 0.2);
            position: relative;
            overflow: hidden;
        }
        .header::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1));
        }
        .error-type { font-family: 'JetBrains Mono', monospace; font-size: 14px; opacity: 0.8; margin-bottom: 10px; }
        .error-msg { font-size: 32px; font-weight: 800; letter-spacing: -1px; margin-bottom: 20px; }
        .error-loc { 
            background: rgba(0,0,0,0.2);
            padding: 10px 20px;
            border-radius: 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            display: inline-block;
        }

        .section {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .tabs { display: flex; background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--border); }
        .tab { 
            padding: 15px 25px; cursor: pointer; font-size: 14px; font-weight: 600; color: var(--muted);
            transition: all 0.3s;
        }
        .tab.active { color: #fff; background: rgba(255,255,255,0.05); }
        .tab-content { display: none; padding: 20px; }
        .tab-content.active { display: block; }

        .code-snippet { background: #000; border-radius: 16px; overflow: hidden; font-family: 'JetBrains Mono', monospace; font-size: 13px; }
        .code-line { display: flex; min-height: 24px; }
        .code-line.highlight { background: rgba(239, 68, 68, 0.15); border-left: 3px solid var(--danger); }
        .line-num { min-width: 50px; padding-right: 15px; text-align: right; color: #4b5563; user-select: none; }
        .line-code { white-space: pre; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 12px; color: var(--muted); border-bottom: 1px solid var(--border); width: 200px; }
        td { padding: 12px; border-bottom: 1px solid var(--border); font-family: 'JetBrains Mono', monospace; color: #fff; }

        .footer { text-align: center; margin-top: 40px; padding: 40px; border-top: 1px solid var(--border); }
        .report-btn {
            background: var(--accent); color: #fff; border: none; padding: 12px 30px; border-radius: 12px;
            font-weight: 700; cursor: pointer; transition: transform 0.2s;
        }
        .report-btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="error-type">{$class}</div>
            <div class="error-msg">{$message}</div>
            <div class="error-loc">
                {$file} : <b>Satır {$line}</b>
            </div>
        </div>
        
        <div class="section">
            <div class="tabs">
                <div class="tab active" onclick="showTab('code')">Kod Akışı</div>
                <div class="tab" onclick="showTab('trace')">Stack Trace</div>
                <div class="tab" onclick="showTab('request')">Request</div>
                <div class="tab" onclick="showTab('server')">Server</div>
            </div>
            
            <div id="tab-code" class="tab-content active">
                <div class="code-snippet">{$codeSnippet}</div>
            </div>
            
            <div id="tab-trace" class="tab-content">{$traceHtml}</div>
            
            <div id="tab-request" class="tab-content">
                <table>{$requestInfo}</table>
            </div>
            
            <div id="tab-server" class="tab-content">
                <table>{$serverInfo}</table>
            </div>
        </div>

        <div class="footer">
            <button class="report-btn" onclick="sendErrorReport()">📧 Hatayı Raporla</button>
            <p style="margin-top:20px; color:var(--muted); font-size:12px;">
                IEF Framework &copy; 2026 | PHP <?= phpversion() ?>
            </p>
        </div>
    </div>
    
    <script>
        function showTab(name) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('tab-' + name).classList.add('active');
        }
        
        function sendErrorReport() {
            const btn = document.querySelector('button[onclick="sendErrorReport()"]');
            const originalText = btn.innerHTML;
            
            // Loading state
            btn.disabled = true;
            btn.innerHTML = '🔄 Raporlanıyor...';
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
            
            const errorData = {
                exception: {$classJson},
                message: {$messageJson},
                file: {$fileJson},
                line: {$line},
                url: window.location.href,
                userAgent: navigator.userAgent,
                timestamp: new Date().toISOString(),
                trace: {$traceJson},
                request: {$requestJson},
                server: {$serverJson}
            };
            
            fetch('/api/report-error', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(errorData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '✅ Gönderildi';
                    btn.style.background = '#059669'; // Green
                    setTimeout(() => {
                        alert('✅ Hata raporu başarıyla gönderildi!');
                        // Reset button
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        btn.style.background = '#dc2626';
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                    }, 500);
                } else {
                    throw new Error(data.error || 'Bilinmeyen hata');
                }
            })
            .catch(e => {
                btn.innerHTML = '❌ Hata';
                btn.style.background = '#7f1d1d'; // Dark red
                setTimeout(() => {
                    alert('❌ Hata raporu gönderilemedi: ' + e.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.background = '#dc2626';
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }, 500);
            });
        }
    </script>
</body>
</html>
HTML;
    }

    /**
     * Kod snippet'ı al
     */
    protected static function getCodeSnippet(string $file, int $line, int $context = 12): string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return '<div class="code-line"><span class="line-code">Dosya okunamadı</span></div>';
        }

        $lines = file($file);
        $start = max(0, $line - $context - 1);
        $end = min(count($lines), $line + $context);

        $html = '';
        for ($i = $start; $i < $end; $i++) {
            $lineNum = $i + 1;
            $isHighlight = $lineNum === $line;
            $class = $isHighlight ? 'code-line highlight' : 'code-line';
            $content = htmlspecialchars($lines[$i] ?? '');

            $html .= "<div class=\"{$class}\">";
            $html .= "<span class=\"line-num\">{$lineNum}</span>";
            $html .= "<span class=\"line-code\">{$content}</span>";
            $html .= "</div>";
        }

        return $html;
    }

    /**
     * Stack trace HTML oluştur
     */
    protected static function buildTraceHtml(array $trace): string
    {
        $html = '';

        foreach ($trace as $index => $frame) {
            $file = $frame['file'] ?? '[internal]';
            $line = $frame['line'] ?? '?';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';
            $function = $frame['function'] ?? '';
            $args = isset($frame['args']) ? self::formatArgs($frame['args']) : '';

            $call = '';
            if ($class) {
                $call .= "<span class=\"trace-class\">{$class}</span>{$type}";
            }
            $call .= "<span style=\"color:#4ade80\">{$function}</span>";
            if ($args) {
                $call .= "(<span class=\"trace-args\">{$args}</span>)";
            }

            $html .= "<div class=\"trace-item\">";
            $html .= "<span class=\"trace-index\">#{$index}</span>";
            $html .= "<span class=\"trace-file\">" . basename($file) . "</span>";
            $html .= "<span class=\"trace-line\">:{$line}</span>";
            $html .= "<span class=\"trace-function\">{$call}</span>";
            $html .= "</div>";
        }

        return $html ?: '<div class="trace-item">Stack trace yok</div>';
    }

    /**
     * Argümanları formatla
     */
    protected static function formatArgs(array $args): string
    {
        $formatted = [];

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $str = strlen($arg) > 50 ? substr($arg, 0, 50) . '...' : $arg;
                $formatted[] = "'" . htmlspecialchars($str) . "'";
            } elseif (is_int($arg) || is_float($arg)) {
                $formatted[] = (string) $arg;
            } elseif (is_bool($arg)) {
                $formatted[] = $arg ? 'true' : 'false';
            } elseif (is_null($arg)) {
                $formatted[] = 'null';
            } elseif (is_array($arg)) {
                $formatted[] = 'Array(' . count($arg) . ')';
            } elseif (is_object($arg)) {
                $formatted[] = get_class($arg);
            } else {
                $formatted[] = gettype($arg);
            }
        }

        return implode(', ', $formatted);
    }

    /**
     * Request bilgilerini al
     */
    protected static function getRequestInfo(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $methodClass = 'method-' . $method;

        $rows = [
            ['Method', "<span class=\"method {$methodClass}\">{$method}</span>"],
            ['URI', $_SERVER['REQUEST_URI'] ?? '-'],
            ['URL', ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '')],
            ['IP', $_SERVER['REMOTE_ADDR'] ?? '-'],
            ['User Agent', $_SERVER['HTTP_USER_AGENT'] ?? '-'],
            ['Referer', $_SERVER['HTTP_REFERER'] ?? '-'],
        ];

        // GET parametreleri
        if (!empty($_GET)) {
            $rows[] = ['GET', '<pre>' . htmlspecialchars(json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>'];
        }

        // POST parametreleri
        if (!empty($_POST)) {
            $rows[] = ['POST', '<pre>' . htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>'];
        }

        $html = '';
        foreach ($rows as $row) {
            $html .= "<tr><th>{$row[0]}</th><td>{$row[1]}</td></tr>";
        }

        return $html;
    }

    /**
     * Server bilgilerini al
     */
    protected static function getServerInfo(): string
    {
        $rows = [
            ['PHP Version', phpversion()],
            ['Server', $_SERVER['SERVER_SOFTWARE'] ?? '-'],
            ['Document Root', $_SERVER['DOCUMENT_ROOT'] ?? '-'],
            ['Script', $_SERVER['SCRIPT_FILENAME'] ?? '-'],
            ['Memory Usage', self::formatBytes(memory_get_usage(true))],
            ['Peak Memory', self::formatBytes(memory_get_peak_usage(true))],
            ['Time', date('Y-m-d H:i:s')],
            ['Timezone', date_default_timezone_get()],
        ];

        // Loaded extensions
        $extensions = get_loaded_extensions();
        sort($extensions);
        $rows[] = ['Extensions', implode(', ', array_slice($extensions, 0, 20)) . (count($extensions) > 20 ? '...' : '')];

        $html = '';
        foreach ($rows as $row) {
            $html .= "<tr><th>{$row[0]}</th><td>{$row[1]}</td></tr>";
        }

        return $html;
    }

    /**
     * Request verilerini topla (Array)
     */
    protected static function collectRequestData(): array
    {
        return [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri' => $_SERVER['REQUEST_URI'] ?? '-',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '-',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '-',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '-',
            'headers' => function_exists('getallheaders') ? getallheaders() : [],
            'get' => $_GET,
            'post' => $_POST,
        ];
    }

    /**
     * Server verilerini topla (Array)
     */
    protected static function collectServerData(): array
    {
        return [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? '-',
            'memory_usage' => self::formatBytes(memory_get_usage(true)),
            'time' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Byte'ı okunabilir formata çevir
     */
    protected static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Production hata sayfası
     */
    protected static function renderProductionPage(int $statusCode): void
    {
        $title = match ($statusCode) {
            404 => 'Sayfa Bulunamadı',
            403 => 'Erişim Reddedildi',
            401 => 'Yetkilendirme Gerekli',
            500 => 'Sunucu Hatası',
            503 => 'Servis Kullanılamıyor',
            default => 'Bir Hata Oluştu'
        };

        $message = match ($statusCode) {
            404 => 'Aradığınız sayfa bulunamadı.',
            403 => 'Bu sayfaya erişim yetkiniz yok.',
            401 => 'Bu işlem için giriş yapmanız gerekiyor.',
            500 => 'Sunucuda beklenmeyen bir hata oluştu.',
            503 => 'Servis geçici olarak kullanılamıyor.',
            default => 'İsteğiniz işlenirken bir hata oluştu.'
        };

        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 40px;
        }
        .code {
            font-size: 120px;
            font-weight: 700;
            color: #dc2626;
            line-height: 1;
        }
        .title {
            font-size: 24px;
            color: #1f2937;
            margin: 20px 0 10px;
        }
        .message {
            color: #6b7280;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
        }
        .button:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">{$statusCode}</div>
        <h1 class="title">{$title}</h1>
        <p class="message">{$message}</p>
        <a href="/" class="button">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
HTML;
    }
}
