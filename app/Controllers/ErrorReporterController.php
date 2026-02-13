<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\MailService;

/**
 * ErrorReporterController
 * Hata raporlama API'si
 */
class ErrorReporterController extends Controller
{
    private const REPORT_EMAIL = 'cozanqel0811@gmail.com';

    /**
     * Hata raporu gönder
     */
    /**
     * Hata raporu gönder
     */
    public function report(): void
    {
        header('Content-Type: application/json');

        try {
            // JSON body parse
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                echo json_encode(['success' => false, 'error' => 'Geçersiz veri']);
                return;
            }

            // Mail içeriği hazırla
            $data = [
                'exception' => htmlspecialchars($input['exception'] ?? 'Unknown'),
                'message' => htmlspecialchars($input['message'] ?? 'No message'),
                'file' => htmlspecialchars($input['file'] ?? 'Unknown'),
                'line' => (int) ($input['line'] ?? 0),
                'url' => htmlspecialchars($input['url'] ?? 'Unknown'),
                'userAgent' => htmlspecialchars($input['userAgent'] ?? 'Unknown'),
                'timestamp' => htmlspecialchars($input['timestamp'] ?? date('c')),
                'trace' => $input['trace'] ?? [], // Array olarak al
                'request' => $input['request'] ?? [], // Array olarak al
                'server' => $input['server'] ?? [], // Array olarak al
            ];

            $subject = "🔴 Hata Raporu: {$data['exception']}";

            $body = $this->getEmailTemplate($data);

            // Mail gönder
            $mailService = new MailService();
            $result = $mailService->send(self::REPORT_EMAIL, $subject, $body);

            if ($result) {
                // Log'a da yaz
                $this->logError($input);
                echo json_encode(['success' => true, 'message' => 'Hata raporu gönderildi']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Email gönderilemedi']);
            }

        } catch (\Throwable $e) {
            error_log("Error report failed: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Sistem hatası: ' . $e->getMessage()]);
        }
    }

    /**
     * Email template
     */
    private function getEmailTemplate(array $data): string
    {
        // Stack Trace HTML oluştur (İlk 10 satır)
        $traceHtml = '';
        if (!empty($data['trace']) && is_array($data['trace'])) {
            foreach (array_slice($data['trace'], 0, 10) as $i => $frame) {
                $file = basename($frame['file'] ?? '[internal]');
                $line = $frame['line'] ?? '?';
                $callee = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '');

                $traceHtml .= "
                <tr>
                    <td style='padding:4px 8px;border-bottom:1px solid #333;color:#888;'>#{$i}</td>
                    <td style='padding:4px 8px;border-bottom:1px solid #333;color:#ccc;'>
                        <div style='color:#fca5a5;font-weight:bold;'>{$callee}</div>
                        <div style='color:#6b7280;font-size:11px;'>{$file}:{$line}</div>
                    </td>
                </tr>";
            }
        } else {
            $traceHtml = "<tr><td colspan='2' style='padding:8px;color:#888;'>Stack trace bulunamadı.</td></tr>";
        }

        // Headers HTML
        $headersHtml = '';
        if (!empty($data['request']['headers']) && is_array($data['request']['headers'])) {
            foreach ($data['request']['headers'] as $key => $val) {
                $headersHtml .= "
                <tr>
                    <td style='padding:4px 8px;border-bottom:1px solid #2d2d2d;color:#888;'>{$key}</td>
                    <td style='padding:4px 8px;border-bottom:1px solid #2d2d2d;color:#ccc;'>{$val}</td>
                </tr>";
            }
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #1a1a1a; margin: 0; padding: 20px; color: #ccc; }
    .container { max-width: 800px; margin: 0 auto; background: #0f0f11; border: 1px solid #333; border-radius: 8px; overflow: hidden; }
    .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 30px; color: white; }
    .badge { background: rgba(0,0,0,0.3); padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-bottom: 10px; }
    .title { font-size: 24px; font-weight: bold; margin: 0 0 10px 0; }
    .subtitle { font-family: monospace; opacity: 0.9; }
    .content { padding: 20px; }
    .section { margin-bottom: 25px; }
    .section-title { font-size: 14px; font-weight: bold; color: #fff; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 12px; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; font-family: monospace; }
    .kv-table td { padding: 6px 0; border-bottom: 1px solid #222; }
    .kv-key { width: 140px; color: #666; }
    .kv-val { color: #ccc; }
    .footer { background: #111; padding: 15px; text-align: center; font-size: 11px; color: #555; border-top: 1px solid #222; }
    a { color: #dc2626; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="badge">Sistem Hatası</div>
        <div class="title">Error</div>
        <div class="subtitle">{$data['message']}</div>
    </div>
    
    <div class="content">
        <div class="section">
            <div class="section-title">📌 Hata Detayı</div>
            <table class="data-table kv-table">
                <tr><td class="kv-key">Mesaj</td><td class="kv-val" style="color:#f87171;font-weight:bold;">{$data['message']}</td></tr>
                <tr><td class="kv-key">Dosya</td><td class="kv-val">{$data['file']}</td></tr>
                <tr><td class="kv-key">Satır</td><td class="kv-val">{$data['line']}</td></tr>
                <tr><td class="kv-key">URL</td><td class="kv-val"><a href="{$data['url']}">{$data['url']}</a></td></tr>
                <tr><td class="kv-key">Zaman</td><td class="kv-val">{$data['timestamp']}</td></tr>
                <tr><td class="kv-key">PHP</td><td class="kv-val">{$data['server']['php_version']}</td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">🌐 HTTP Headers</div>
            <table class="data-table">
                {$headersHtml}
            </table>
        </div>

        <div class="section">
            <div class="section-title">📚 Stack Trace (İlk 10 Satır)</div>
            <table class="data-table">
                {$traceHtml}
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">⚙️ Sistem Bilgisi</div>
            <table class="data-table kv-table">
                <tr><td class="kv-key">Server</td><td class="kv-val">{$data['server']['server_software']}</td></tr>
                <tr><td class="kv-key">Memory</td><td class="kv-val">{$data['server']['memory_usage']}</td></tr>
            </table>
        </div>
    </div>
    
    <div class="footer">
        Bu email otomatik olarak hata raporlama sistemi tarafından gönderilmiştir.<br>
        IEF Framework © 2026
    </div>
</div>
</body>
</html>
HTML;
    }

    /**
     * Hata logla
     */
    private function logError(array $data): void
    {
        $logFile = dirname(__DIR__, 2) . '/storage/logs/error-reports-' . date('Y-m') . '.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d (URL: %s)\n",
            date('Y-m-d H:i:s'),
            $data['exception'] ?? 'Unknown',
            $data['message'] ?? 'No message',
            $data['file'] ?? 'Unknown',
            $data['line'] ?? 0,
            $data['url'] ?? 'Unknown'
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}