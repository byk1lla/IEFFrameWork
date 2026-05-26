<?php
/**
 * ErrorReporterController — exception sayfasından gelen hata raporlarını
 * DB'ye yazar ve (yapılandırılmışsa) admin'e e-posta gönderir.
 *
 *  POST /api/report-error   — exception sayfasındaki "Hatayı Raporla" butonu
 *
 * Ayarlar — Admin > Ayarlar > Mail:
 *   - mail.error_reporter_enabled  (bool, default true)
 *   - mail.error_reporter_to       (string — boşsa mail.admin_inbox kullanılır)
 *   - mail.error_reporter_max_per_day (int, default 5 — aynı hash için günde max kaç mail)
 *
 * Rate limit: aynı IP saatte max 20 rapor.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ErrorReport;
use App\Models\Setting;
use App\Services\MailService;

class ErrorReporterController extends Controller
{
    public function report(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input') ?: '', true) ?: [];

            // Rate limit (saatlik IP)
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            if (!$this->checkRateLimit($ip)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'error' => 'Çok fazla rapor — saatte 20 limit aşıldı.']);
                return;
            }

            // Minimum doğrulama
            if (empty($input['exception']) || empty($input['file'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'exception ve file zorunlu.']);
                return;
            }

            // DB'ye kaydet (upsert)
            $result = ErrorReport::record([
                'exception'    => (string) $input['exception'],
                'message'      => (string) ($input['message'] ?? ''),
                'file'         => (string) $input['file'],
                'line'         => (int) ($input['line'] ?? 0),
                'trace'        => $input['trace'] ?? null,
                'request'      => array_filter([
                    'url'         => $input['url'] ?? ($_SERVER['REQUEST_URI'] ?? null),
                    'method'      => $_SERVER['REQUEST_METHOD'] ?? null,
                    'userAgent'   => $input['userAgent'] ?? null,
                    'timestamp'   => $input['timestamp'] ?? date('c'),
                    'body'        => $input['request'] ?? null,
                ]),
                'server'       => array_filter([
                    'php_version'        => PHP_VERSION,
                    'os'                 => PHP_OS,
                    'framework_version'  => \App\Core\Config::get('app.version', '2.0.0'),
                    'sapi'               => PHP_SAPI,
                    'memory_peak_mb'     => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                    'snapshot'           => $input['server'] ?? null,
                ]),
                'user_note'    => (string) ($input['user_note'] ?? ''),
                'reporter_ip'  => $ip,
            ]);

            // E-posta bildirimi (yapılandırılmışsa + günlük limit dolmamışsa)
            $mailSent = $this->maybeSendMail($result, $input);

            echo json_encode([
                'success'   => true,
                'id'        => $result['id'],
                'is_new'    => $result['is_new'],
                'count'     => $result['count'],
                'mail_sent' => $mailSent,
                'message'   => $result['is_new']
                    ? 'Hata kaydedildi.'
                    : "Aynı hata daha önce raporlanmış (toplam {$result['count']} kez).",
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => 'Rapor kaydedilemedi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Saatlik rate limit — basit dosya tabanlı sayım.
     */
    protected function checkRateLimit(string $ip): bool
    {
        if ($ip === '') return true;
        $key  = 'rl_error_' . md5($ip) . '_' . date('YmdH');
        $file = STORAGE_PATH . '/cache/' . $key . '.txt';
        $dir  = dirname($file);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $count = is_file($file) ? (int) file_get_contents($file) : 0;
        if ($count >= 20) return false;

        file_put_contents($file, (string) ($count + 1));
        return true;
    }

    /**
     * E-posta bildirim mantığı — günlük max sayıya göre throttle.
     */
    protected function maybeSendMail(array $result, array $input): bool
    {
        try {
            $enabled = (bool) Setting::get('mail.error_reporter_enabled', '1');
            if (!$enabled) return false;

            $to = trim((string) Setting::get('mail.error_reporter_to', ''));
            if ($to === '') $to = trim((string) Setting::get('mail.admin_inbox', ''));
            if ($to === '') return false;

            $maxPerDay = (int) Setting::get('mail.error_reporter_max_per_day', '5');

            // Bu hash için bugün kaç mail gönderildi?
            $today = date('Y-m-d');
            $row = Database::getInstance()->fetch(
                "SELECT mail_sent_count, last_seen_at FROM error_reports WHERE id = ?",
                [$result['id']]
            );
            $sentToday = (int) ($row['mail_sent_count'] ?? 0);

            // Yeni hata her zaman gönder; tekrarlanan hatalar günlük limit dahilinde
            if (!$result['is_new'] && $sentToday >= $maxPerDay) return false;

            $subject = $result['is_new']
                ? '🔴 [YENİ] ' . ($input['exception'] ?? 'Hata') . ' — ' . basename((string) ($input['file'] ?? ''))
                : '🔁 [' . $result['count'] . 'x] ' . ($input['exception'] ?? 'Hata');

            $body = $this->buildMailBody($result, $input);

            (new MailService())->send([
                'to'       => $to,
                'subject'  => $subject,
                'body'     => $body,
                'is_html'  => true,
            ]);

            ErrorReport::incrementMailSent($result['id']);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function buildMailBody(array $result, array $input): string
    {
        $e = htmlspecialchars((string) ($input['exception'] ?? 'Unknown'), ENT_QUOTES);
        $m = htmlspecialchars((string) ($input['message'] ?? ''), ENT_QUOTES);
        $f = htmlspecialchars((string) ($input['file'] ?? ''), ENT_QUOTES);
        $l = (int) ($input['line'] ?? 0);
        $url = htmlspecialchars((string) ($input['url'] ?? ''), ENT_QUOTES);
        $ua  = htmlspecialchars((string) ($input['userAgent'] ?? ''), ENT_QUOTES);
        $note = trim((string) ($input['user_note'] ?? ''));
        $tsStr = htmlspecialchars((string) ($input['timestamp'] ?? date('c')), ENT_QUOTES);
        $count = (int) $result['count'];

        $trace = '';
        if (!empty($input['trace']) && is_array($input['trace'])) {
            foreach (array_slice($input['trace'], 0, 15) as $i => $t) {
                $tFile = htmlspecialchars((string) ($t['file'] ?? ''), ENT_QUOTES);
                $tLine = (int) ($t['line'] ?? 0);
                $tFunc = htmlspecialchars((string) ($t['function'] ?? ''), ENT_QUOTES);
                $trace .= "<tr><td style='color:#94a3b8;text-align:right;padding:4px 8px'>#{$i}</td>"
                        . "<td style='font-family:monospace;font-size:12px;padding:4px 8px'>"
                        . ($tFile ? "{$tFile}<span style='color:#94a3b8'>:{$tLine}</span><br>" : '')
                        . "<span style='color:#1d4ed8'>{$tFunc}</span></td></tr>";
            }
        }

        $noteHtml = $note !== ''
            ? '<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px;margin:16px 0">
                 <div style="font:600 11px Inter;color:#92400e;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px">Kullanıcı Notu</div>
                 <div style="font:15px Inter;color:#451a03">' . htmlspecialchars($note, ENT_QUOTES) . '</div>
               </div>'
            : '';

        $adminUrl = htmlspecialchars(($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/admin/error-reports/' . $result['id'], ENT_QUOTES);

        return <<<HTML
<!doctype html>
<html><body style="margin:0;background:#f8fafc;font-family:Inter,system-ui,sans-serif">
<div style="max-width:680px;margin:24px auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0">

  <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);padding:24px 28px;color:#fff">
    <div style="font:700 12px Inter;text-transform:uppercase;letter-spacing:.1em;opacity:.85">Hata Raporu · {$count}x</div>
    <h1 style="font:800 22px Inter;margin:8px 0 4px;letter-spacing:-.02em">{$e}</h1>
    <div style="font:14px Inter;opacity:.95">{$m}</div>
  </div>

  <div style="padding:24px 28px">
    {$noteHtml}

    <table style="width:100%;font:13.5px Inter;border-collapse:collapse">
      <tr><td style="padding:6px 0;width:120px;color:#64748b">Dosya</td><td style="font-family:monospace;font-size:12.5px;color:#0f172a">{$f}<span style='color:#dc2626'>:{$l}</span></td></tr>
      <tr><td style="padding:6px 0;color:#64748b">URL</td><td style="font-family:monospace;font-size:12.5px;color:#0f172a">{$url}</td></tr>
      <tr><td style="padding:6px 0;color:#64748b">Tarih</td><td style="font-family:monospace;font-size:12.5px;color:#0f172a">{$tsStr}</td></tr>
      <tr><td style="padding:6px 0;color:#64748b">Tarayıcı</td><td style="font-size:12.5px;color:#475569;word-break:break-all">{$ua}</td></tr>
    </table>

    <h3 style="font:700 12px Inter;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:24px 0 8px">Stack Trace</h3>
    <table style="width:100%;background:#0f172a;color:#e2e8f0;border-radius:10px;border-collapse:collapse">
      {$trace}
    </table>

    <div style="text-align:center;margin-top:28px">
      <a href="{$adminUrl}" style="display:inline-block;background:#0f172a;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font:600 14px Inter">Admin'de Aç →</a>
    </div>
  </div>

  <div style="background:#f8fafc;padding:14px 28px;font:11.5px Inter;color:#94a3b8;text-align:center;border-top:1px solid #e2e8f0">
    IEF Framework Error Reporter · Raporları kapatmak için Admin → Ayarlar → Mail
  </div>
</div>
</body></html>
HTML;
    }
}
