# Rate Limiting

- [Giriş](#giris)
- [Form Rate Limit](#form-rate-limit)
- [Login Rate Limit](#login-rate-limit)
- [Custom Rate Limiter](#custom-rate-limiter)

---

## Giriş

IEF, brute-force ve spam saldırılarına karşı IP bazlı rate limit sağlar. Konfigürasyon **Admin > Ayarlar > Güvenlik** sekmesinden yapılır.

---

## Form Rate Limit

Settings > Güvenlik > **Form Gönderim Limiti** (default: saatte 5).

İletişim formu, randevu talebi vb. public POST endpoint'lerde:

```php
public function submit(): Response
{
    $this->abortIfInvalidCsrf();

    $limit = (int) Setting::get('security.rate_limit_per_hour', 5);
    $ip    = $this->request->ip();
    $key   = "rl:form:{$ip}:" . date('YmdH');

    $count = Cache::get($key, 0);
    if ($count >= $limit) {
        $this->flash('error', 'Çok fazla istek. Saatte ' . $limit . ' kez deneyebilirsin.');
        return $this->redirect('/iletisim');
    }
    Cache::put($key, $count + 1, 3600);

    // ... form işleme
}
```

> Cache henüz built-in değil — şu an basitçe storage/cache veya session ile sayım yapabilirsin.

---

## Login Rate Limit

Settings > Güvenlik > **Max Login Denemesi** (default: 5) + **Lockout Süresi (dk)** (default: 15).

`AuthController@login`'de:

```php
$ip       = $this->request->ip();
$attempts = (int) Session::get("login_fail:{$ip}", 0);
$max      = (int) Setting::get('security.max_login_attempts', 5);

if ($attempts >= $max) {
    $lockout = (int) Setting::get('security.lockout_minutes', 15);
    $this->flash('error', "Çok fazla başarısız deneme. {$lockout} dakika bekleyin.");
    return $this->redirect('/login');
}

// ... login attempt
if (failed) {
    Session::set("login_fail:{$ip}", $attempts + 1);
} else {
    Session::remove("login_fail:{$ip}");
}
```

---

## Custom Rate Limiter

İhtiyaca göre kendi rate limiter'ını yaz:

```php
class RateLimiter
{
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $file = STORAGE_PATH . '/cache/rl_' . md5($key) . '.json';
        $data = is_file($file) ? json_decode(file_get_contents($file), true) : ['count' => 0, 'reset' => 0];

        if ($data['reset'] < time()) {
            $data = ['count' => 0, 'reset' => time() + $decaySeconds];
        }

        $data['count']++;
        file_put_contents($file, json_encode($data));

        return $data['count'] > $maxAttempts;
    }
}

// Kullanım:
if (RateLimiter::tooManyAttempts('api:' . $ip, 60, 60)) {
    abort(429);
}
```

> Production'da Redis tabanlı bir limiter (token bucket veya leaky bucket) önerilir.

---

**Sonraki:** [Mail →](mail.md)
