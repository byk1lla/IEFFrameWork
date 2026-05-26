# Hata Yönetimi

- [Giriş](#giris)
- [Debug Modu](#debug-modu)
- [Exception Handler](#exception-handler)
- [HTTP Hata Sayfaları](#http-hata-sayfalari)
- [Manuel Exception Fırlatma](#manuel-exception-frlatma)
- [Logging](#logging)

---

## Giriş

Yeni bir IEF projesi başlattığında, hata ve exception handling senin için zaten yapılandırılmıştır. `App\Core\ExceptionHandler` sınıfı, uygulamanın fırlattığı tüm exception'ları yakalayıp loglayan ve kullanıcıya render eden merkezi noktadır.

---

## Debug Modu

`config/app.php`'deki `debug` ayarı, kullanıcının hata hakkında ne kadar bilgi göreceğini kontrol eder:

```php
return [
    'debug' => true,   // development
    // 'debug' => false,  // production
];
```

| Debug | Kullanıcı görür |
|---|---|
| `true` | Tam stack trace + dosya/satır + değişkenler — geliştirme için |
| `false` | Sadece nazik hata sayfası ("Bir şeyler ters gitti") — production için |

> **Asla** production'da `debug: true` çalıştırma — DB şifresi, dosya yolları, kullanıcı verisi sızabilir.

---

## Exception Handler

Tüm fırlatılmamış exception'lar `app/Core/ExceptionHandler::handle()` tarafından yakalanır:

```php
// index.php
try {
    (new App())->run();
} catch (\Throwable $e) {
    ExceptionHandler::handle($e);
}
```

Handler:
1. Exception'ı `storage/logs/app-YYYY-MM-DD.log`'a yazar
2. Debug açıksa modern, syntax-highlighted hata sayfası gösterir
3. Debug kapalıysa `app/Views/errors/500.php` render eder

### Özelleştirme

Production hata sayfasını değiştirmek için `app/Views/errors/500.php` template'ini düzenle.

---

## HTTP Hata Sayfaları

Belirli HTTP status code'ları için ayrı sayfalar:

```
app/Views/errors/
├── 404.php           # Bulunamadı
├── 419.php           # CSRF token süresi doldu
├── 500.php           # Sunucu hatası
└── maintenance.php   # 503 — bakım modu
```

Bunları kendi tasarımına göre düzenleyebilirsin. `404.php` örneği:

```blade
@extends('layouts.app')
@section('content')
    <div class="text-center py-20">
        <h1 class="text-9xl font-bold text-slate-300">404</h1>
        <p class="text-xl text-slate-600 mt-4">Aradığın sayfa bulunamadı.</p>
        <a href="/" class="mt-6 inline-block px-6 py-3 bg-brand-600 text-white rounded-lg">Anasayfa</a>
    </div>
@endsection
```

---

## Manuel Exception Fırlatma

```php
abort(404);                            // Generic 404
abort(404, 'Bu yazı kaldırılmış.');    // Özel mesaj
abort(403);                            // Yetkin yok
abort(419);                            // CSRF

// Koşullu:
abort_if($user->banned, 403);
abort_unless($post, 404);

// Doğrudan exception:
throw new \RuntimeException('Beklenmeyen durum');
```

`abort()` bir `HttpException` fırlatır; ExceptionHandler bunu yakalar ve uygun status code + sayfayla yanıt verir.

---

## Logging

Tüm yakalanan exception'lar otomatik loglanır. Manuel logging:

```php
use App\Core\Logger;

Logger::info('Kullanıcı kayıt oldu', ['email' => $email]);
Logger::warning('Olağandışı istek', ['ip' => $ip]);
Logger::error('Ödeme başarısız', ['order_id' => $orderId, 'reason' => $e->getMessage()]);
```

Loglar `storage/logs/app-YYYY-MM-DD.log` dosyasına Monolog formatında yazılır. Admin > Loglar sayfasından görüntülenebilir.

> Detay: [Logging →](logging.md)

---

**Sonraki:** [Logging →](logging.md)
