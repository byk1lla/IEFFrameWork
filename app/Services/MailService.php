<?php

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;

/**
 * MailService for IEF Framework
 */
class MailService
{
    protected array $config;

    public function __construct()
    {
        $this->config = Config::get('mail', [
            'from_email' => 'noreply@iefframework.com',
            'from_name' => 'IEF Framework',
            'driver' => 'mail' // Options: mail, smtp (smtp not implemented in this basic version)
        ]);
    }

    public function send(string $to, string $subject, string $body, array $headers = []): bool
    {
        try {
            $from = "{$this->config['from_name']} <{$this->config['from_email']}>";

            $defaultHeaders = [
                'From' => $from,
                'Reply-To' => $from,
                'X-Mailer' => 'IEF Framework Mailer',
                'MIME-Version' => '1.0',
                'Content-Type' => 'text/html; charset=UTF-8'
            ];

            $allHeaders = array_merge($defaultHeaders, $headers);
            $headerString = "";
            foreach ($allHeaders as $key => $value) {
                $headerString .= "{$key}: {$value}\r\n";
            }

            if ($this->config['driver'] === 'mail') {
                return mail($to, $subject, $body, $headerString);
            }

            Logger::error("Mail driver '{$this->config['driver']}' not supported.");
            return false;

        } catch (\Exception $e) {
            Logger::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
