<?php
/**
 * HTTP İstek Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Request
{
    protected static ?Request $instance = null;
    protected array $get;
    protected array $post;
    protected array $files;
    protected array $server;
    protected array $headers;
    protected ?array $json = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
    }

    public static function getInstance(): Request
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function uri(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->method()) === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isHtmx(): bool
    {
        return $this->header('HX-Request') === 'true';
    }

    public function isJson(): bool
    {
        return str_contains($this->header('Content-Type', ''), 'application/json');
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $this->json()[$key] ?? $default;
    }

    /**
     * input() metodunun alias'ı
     */
    public function get(string $key, $default = null)
    {
        return $this->input($key, $default);
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json() ?? []);
    }

    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    public function except(array $keys): array
    {
        $all = $this->all();
        return array_diff_key($all, array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->get[$key]);
    }

    public function query(?string $key = null, $default = null)
    {
        if ($key === null) return $this->get;
        return $this->get[$key] ?? $default;
    }

    public function post(?string $key = null, $default = null)
    {
        if ($key === null) return $this->post;
        return $this->post[$key] ?? $default;
    }

    /**
     * Request verilerine yeni değerler ekle veya mevcut değerleri güncelle
     * @param array $data Eklenecek/güncellenecek veriler
     * @return self
     */
    public function merge(array $data): self
    {
        $this->post = array_merge($this->post, $data);
        return $this;
    }

    public function json(): ?array
    {
        if ($this->json === null && $this->isJson()) {
            $content = file_get_contents('php://input');
            $this->json = json_decode($content, true) ?? [];
        }
        return $this->json;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function header(string $key, $default = null): ?string
    {
        // Gelen anahtarı büyük harfe çevir ve alt çizgileri tireye çevir (parseHeaders ile uyumlu olması için)
        $key = strtoupper(str_replace('_', '-', $key));
        
        // Eğer anahtar hala HTTP- ile başlıyorsa temizle (parseHeaders HTTP_ kısmını attığı için)
        if (str_starts_with($key, 'HTTP-')) {
            $key = substr($key, 5);
        }
        
        return $this->headers[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('Authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

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

    public function schemeAndHttpHost(): string
    {
        $scheme = ($this->server['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }

    public function url(): string
    {
        return $this->schemeAndHttpHost() . $this->uri();
    }

    public function fullUrl(): string
    {
        $query = $this->server['QUERY_STRING'] ?? '';
        return $this->url() . ($query ? '?' . $query : '');
    }

    /**
     * Request verilerini doğrula ve döndür
     * @param array $rules Validasyon kuralları
     * @return array Doğrulanmış veriler
     * @throws \Exception Validasyon hatası durumunda
     */
    public function validate(array $rules): array
    {
        $data = $this->all();
        $validator = new Validator($data, $rules);
        
        if (!$validator->validate()) {
            $errors = $validator->errors();
            $firstError = reset($errors);
            $errorMsg = is_array($firstError) ? $firstError[0] : $firstError;
            throw new \Exception($errorMsg ?? 'Validasyon hatası');
        }
        
        // Sadece kurallarda belirtilen alanları döndür
        return array_intersect_key($data, $rules);
    }
}
