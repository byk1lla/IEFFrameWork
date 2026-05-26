<?php
/**
 * Request — HTTP isteğini soyutlar.
 *
 * Singleton (Request::getInstance()). Router method-injection ile
 * controller'lara verir.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    protected static ?Request $instance = null;

    protected array $get;
    protected array $post;
    protected array $files;
    protected array $server;
    protected array $cookies;
    protected array $headers;
    protected ?array $json = null;

    public function __construct()
    {
        $this->get     = $_GET     ?? [];
        $this->post    = $_POST    ?? [];
        $this->files   = $_FILES   ?? [];
        $this->server  = $_SERVER  ?? [];
        $this->cookies = $_COOKIE  ?? [];
        $this->headers = $this->parseHeaders();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace('_', '-', substr($k, 5));
                $headers[strtoupper($name)] = $v;
            }
        }
        // CONTENT_TYPE & CONTENT_LENGTH HTTP_ prefix'i taşımaz
        if (isset($this->server['CONTENT_TYPE']))   $headers['CONTENT-TYPE']   = $this->server['CONTENT_TYPE'];
        if (isset($this->server['CONTENT_LENGTH'])) $headers['CONTENT-LENGTH'] = $this->server['CONTENT_LENGTH'];
        return $headers;
    }

    // ─── Method & URI ──────────────────────────────────────────────
    public function method(): string
    {
        $m = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($m === 'POST') {
            $override = $this->post['_method'] ?? $this->header('X-HTTP-Method-Override');
            if ($override) $m = strtoupper($override);
        }
        return $m;
    }

    public function isMethod(string $method): bool { return $this->method() === strtoupper($method); }
    public function isGet(): bool                  { return $this->isMethod('GET'); }
    public function isPost(): bool                 { return $this->isMethod('POST'); }
    public function isPut(): bool                  { return $this->isMethod('PUT'); }
    public function isDelete(): bool               { return $this->isMethod('DELETE'); }

    public function uri(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public function isAjax(): bool { return $this->header('X-Requested-With') === 'XMLHttpRequest'; }
    public function isHtmx(): bool { return $this->header('HX-Request') === 'true'; }
    public function isJson(): bool { return str_contains((string) $this->header('Content-Type', ''), 'application/json'); }

    // ─── Input ─────────────────────────────────────────────────────
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? ($this->json()[$key] ?? $default);
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json() ?? []);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->get[$key]) || isset(($this->json() ?? [])[$key]);
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->get : ($this->get[$key] ?? $default);
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->post : ($this->post[$key] ?? $default);
    }

    public function merge(array $data): self
    {
        $this->post = array_merge($this->post, $data);
        return $this;
    }

    public function json(): ?array
    {
        if ($this->json === null && $this->isJson()) {
            $body = file_get_contents('php://input') ?: '';
            $this->json = json_decode($body, true) ?? [];
        }
        return $this->json;
    }

    // ─── Files ─────────────────────────────────────────────────────
    public function file(string $key): ?array      { return $this->files[$key] ?? null; }
    public function hasFile(string $key): bool     { return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK; }
    public function allFiles(): array              { return $this->files; }

    // ─── Headers / Cookies / Server ────────────────────────────────
    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtoupper(str_replace('_', '-', $key));
        return $this->headers[$key] ?? $default;
    }

    public function allHeaders(): array  { return $this->headers; }
    public function cookie(string $key, mixed $default = null): mixed { return $this->cookies[$key] ?? $default; }
    public function server(string $key, mixed $default = null): mixed { return $this->server[$key] ?? $default; }

    public function bearerToken(): ?string
    {
        $auth = (string) $this->header('Authorization', '');
        return str_starts_with($auth, 'Bearer ') ? substr($auth, 7) : null;
    }

    // ─── URL & IP ──────────────────────────────────────────────────
    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['HTTP_CLIENT_IP']
            ?? $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function schemeAndHost(): string
    {
        $secure = ($this->server['HTTPS'] ?? '') === 'on'
               || ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        return ($secure ? 'https' : 'http') . '://' . ($this->server['HTTP_HOST'] ?? 'localhost');
    }

    public function url(): string      { return $this->schemeAndHost() . $this->uri(); }
    public function fullUrl(): string  { $q = $this->server['QUERY_STRING'] ?? ''; return $this->url() . ($q ? '?' . $q : ''); }

    // ─── Validation kısayolu ───────────────────────────────────────
    public function validate(array $rules, array $messages = []): array
    {
        $v = new Validator($this->all(), $rules, $messages);
        if ($v->fails()) {
            $errs = $v->errors();
            $first = reset($errs);
            $msg = is_array($first) ? ($first[0] ?? 'Validasyon hatası') : (string) $first;
            throw new \RuntimeException($msg);
        }
        return array_intersect_key($this->all(), $rules);
    }
}
