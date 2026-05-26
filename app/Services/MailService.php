<?php
/**
 * MailService — PHPMailer ile mail göndericisi.
 *
 * Driver'lar (Setting::get('mail.driver') veya config/mail.php fallback):
 *   - 'log'  → storage/logs/mail-YYYY-MM-DD.log (dev için default)
 *   - 'mail' → PHP mail() (PHPMailer'ın native mail() backend'i)
 *   - 'smtp' → PHPMailer ile SMTP (Settings/UI'dan host/port/user/pass)
 *
 * Kullanım:
 *   MailService::send($to, $subject, $body, ['html'=>true, 'reply_to'=>'...']);
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Models\Setting;
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    public static function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $cfg = self::resolveConfig();
        $isHtml   = (bool) ($options['html'] ?? false);
        $from     = (string) ($options['from']      ?? $cfg['from_address']);
        $fromName = (string) ($options['from_name'] ?? $cfg['from_name']);
        $replyTo  = $options['reply_to'] ?? null;

        try {
            return match ($cfg['driver']) {
                'smtp' => self::sendPhpMailer($to, $subject, $body, $from, $fromName, $replyTo, $isHtml, $cfg, true),
                'mail' => self::sendPhpMailer($to, $subject, $body, $from, $fromName, $replyTo, $isHtml, $cfg, false),
                default => self::sendLog($to, $subject, $body, $from, $fromName, $replyTo, $isHtml),
            };
        } catch (\Throwable $e) {
            Logger::error('Mail gönderilemedi', ['to' => $to, 'subject' => $subject, 'err' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Ayarları çözümle — önce Settings DB, sonra config/mail.php.
     */
    protected static function resolveConfig(): array
    {
        $s = Setting::group('mail');
        return [
            'driver'        => $s['driver']         ?? config('mail.driver', 'log'),
            'from_address'  => $s['from_address']   ?? config('mail.from.address', 'no-reply@example.com'),
            'from_name'     => $s['from_name']      ?? config('mail.from.name',    'IEF Framework'),
            'smtp_host'     => $s['smtp_host']      ?? config('mail.smtp.host', 'localhost'),
            'smtp_port'     => (int) ($s['smtp_port']  ?? config('mail.smtp.port', 587)),
            'smtp_user'     => $s['smtp_user']      ?? config('mail.smtp.username', ''),
            'smtp_pass'     => $s['smtp_pass']      ?? config('mail.smtp.password', ''),
            'smtp_encryption' => $s['smtp_encryption'] ?? config('mail.smtp.encryption', 'tls'),
        ];
    }

    // ─── Log driver (dev) ───────────────────────────────────────────
    protected static function sendLog(string $to, string $subject, string $body, string $from, string $fromName, ?string $replyTo, bool $isHtml): bool
    {
        $dir = STORAGE_PATH . '/logs';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $file = $dir . '/mail-' . date('Y-m-d') . '.log';

        $entry  = "=== " . date('Y-m-d H:i:s') . " ===\n";
        $entry .= "From: $fromName <$from>\n";
        $entry .= "To:   $to\n";
        if ($replyTo) $entry .= "Reply-To: $replyTo\n";
        $entry .= "Subject: $subject\n";
        $entry .= "Content-Type: " . ($isHtml ? 'text/html' : 'text/plain') . "\n\n";
        $entry .= $body . "\n\n";

        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
        Logger::info('Mail (log driver)', ['to' => $to, 'subject' => $subject]);
        return true;
    }

    // ─── PHPMailer (SMTP veya native mail) ──────────────────────────
    protected static function sendPhpMailer(string $to, string $subject, string $body, string $from, string $fromName, ?string $replyTo, bool $isHtml, array $cfg, bool $useSmtp): bool
    {
        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'base64';

        if ($useSmtp) {
            $mailer->isSMTP();
            $mailer->Host       = $cfg['smtp_host'];
            $mailer->Port       = $cfg['smtp_port'];
            $mailer->SMTPAuth   = $cfg['smtp_user'] !== '';
            $mailer->Username   = $cfg['smtp_user'];
            $mailer->Password   = $cfg['smtp_pass'];
            $mailer->SMTPSecure = match ($cfg['smtp_encryption']) {
                'ssl' => PHPMailer::ENCRYPTION_SMTPS,
                'tls' => PHPMailer::ENCRYPTION_STARTTLS,
                default => '',
            };
            // Geliştirme için: self-signed sertifika sorunlarına tolerans
            if ((string) config('app.env') === 'development') {
                $mailer->SMTPOptions = [
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
                ];
            }
        } else {
            $mailer->isMail();
        }

        $mailer->setFrom($from, $fromName);
        $mailer->addAddress($to);
        if ($replyTo) $mailer->addReplyTo((string) $replyTo);

        $mailer->Subject = $subject;
        if ($isHtml) {
            $mailer->isHTML(true);
            $mailer->Body    = $body;
            $mailer->AltBody = strip_tags(str_replace(["<br>", "<br/>", "<br />"], "\n", $body));
        } else {
            $mailer->Body = $body;
        }

        try {
            $ok = $mailer->send();
            Logger::info('Mail gönderildi', ['driver' => $useSmtp ? 'smtp' : 'mail', 'to' => $to, 'subject' => $subject]);
            return $ok;
        } catch (MailerException $e) {
            Logger::error('PHPMailer hata', ['err' => $mailer->ErrorInfo]);
            return false;
        }
    }
}
