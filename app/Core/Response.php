<?php
/**
 * HTTP Yanıt Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Inject Debug Bar if enabled
        if (ExceptionHandler::isDebug() && strpos($this->content, '</body>') !== false) {
            $debugHtml = DebugBar::getInstance()->render();
            $this->content = str_replace('</body>', $debugHtml . '</body>', $this->content);
        }

        echo $this->content;
    }

    public function json(array $data, int $status = 200): void
    {
        $this->statusCode = $status;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->send();
        exit;
    }

    public function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    public function download(string $filePath, ?string $fileName = null): void
    {
        if (!file_exists($filePath)) {
            $this->json(['error' => 'File not found'], 404);
            return;
        }

        $fileName = $fileName ?? basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0, must-revalidate');

        readfile($filePath);
        exit;
    }

    public function file(string $filePath): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File not found';
            exit;
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
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
        $this->json(array_merge(['success' => true, 'message' => $message], $data), $status);
    }

    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);

        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];

        $message = $message ?: ($messages[$code] ?? 'Error');

        echo "<h1>{$code}</h1><p>{$message}</p>";
        exit;
    }
}
