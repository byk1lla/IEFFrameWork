<?php
/**
 * Response — HTTP yanıt builder'ı.
 *
 * Akıcı API: setStatusCode(), setHeader(), setContent(), send()
 * Kısa yollar: json(), redirect(), back(), download(), file(), abort()
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    protected int    $statusCode = 200;
    protected array  $headers    = [];
    protected string $content    = '';

    public function setStatusCode(int $code): self { $this->statusCode = $code; return $this; }
    public function setHeader(string $name, string $value): self { $this->headers[$name] = $value; return $this; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function getContent(): string { return $this->content; }
    public function getStatusCode(): int { return $this->statusCode; }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $n => $v) header("$n: $v");
        }

        // Debug bar injection (debug + admin toggle + HTML response)
        if (ExceptionHandler::isDebug() && str_contains($this->content, '</body>')) {
            $enabled = true;
            try {
                $v = \App\Models\Setting::get('general.debug_bar');
                // Setting yoksa default true (geri uyum). Açıkça '0' / false ise kapalı.
                if ($v !== null && $v !== '') $enabled = (bool) $v;
            } catch (\Throwable $e) { /* settings tablosu yoksa default açık */ }

            if ($enabled) {
                $bar = DebugBar::getInstance()->render();
                $this->content = str_replace('</body>', $bar . '</body>', $this->content);
            }
        }

        echo $this->content;
    }

    public function json(array $data, int $status = 200): void
    {
        $this->statusCode = $status;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->send();
        exit;
    }

    public function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    public function back(): void
    {
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    public function download(string $path, ?string $filename = null): void
    {
        if (!file_exists($path)) { $this->json(['error' => 'File not found'], 404); return; }
        $filename = $filename ?? basename($path);
        $mime     = mime_content_type($path) ?: 'application/octet-stream';

        header("Content-Type: $mime");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, max-age=0, must-revalidate');
        readfile($path);
        exit;
    }

    public function file(string $path): void
    {
        if (!file_exists($path)) { http_response_code(404); echo 'File not found'; exit; }
        header('Content-Type: ' . (mime_content_type($path) ?: 'application/octet-stream'));
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    public function error(string $message, int $status = 500): void
    {
        $this->json(['error' => $message], $status);
    }

    public function success(string $message, array $data = [], int $status = 200): void
    {
        $this->json(['success' => true, 'message' => $message] + $data, $status);
    }

    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        $defaults = [
            400 => 'Bad Request',  401 => 'Unauthorized', 403 => 'Forbidden',
            404 => 'Not Found',    405 => 'Method Not Allowed',
            419 => 'CSRF Token Mismatch', 422 => 'Unprocessable Entity',
            500 => 'Internal Server Error', 503 => 'Service Unavailable',
        ];
        $message = $message ?: ($defaults[$code] ?? 'Error');
        echo "<h1>$code</h1><p>$message</p>";
        exit;
    }
}
