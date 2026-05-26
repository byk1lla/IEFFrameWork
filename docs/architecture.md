# Mimari Kararlar

- [Felsefe](#felsefe)
- [Service Container Yok](#service-container-yok)
- [.env Yok](#env-yok)
- [Tek Entry Point](#tek-entry-point)
- [Blade-lite (Compile Yok)](#blade-lite-compile-yok)
- [Active Record ORM](#active-record-orm)
- [Standart Komponentler](#standart-komponentler)

---

## Felsefe

IEF Framework 3 prensiple yola çıkar:

1. **Sıfır konfigürasyon** — kutu açılır açılmaz çalışır
2. **Tek paket** — routing, ORM, view, auth, migration, CLI, debug bar hepsi içeride
3. **Okunur kod** — framework çekirdeği bir hafta sonu okunup anlaşılabilir

Bu kararlar bazı modern framework patternlerinin **kasıtlı** olarak dışarıda bırakılmasına yol açar.

---

## Service Container Yok

Laravel/Symfony'deki **service container** ve **dependency injection** patternleri **yoktur**.

### Neden?

- DI container çoğu küçük-orta uygulama için fazlasıyla complex
- "Magic" auto-resolution debug edilmesi zor stack trace'ler üretir
- Constructor injection için sınıfların framework'e bağımlı hale gelmesi gerekir
- Test edilebilirlik genelde manuel mocking ile sağlanır

### Bunun yerine

```php
// Doğrudan new'le
$mail = new MailService();
$mail->send(...);

// Statik facade
Database::getInstance()->fetchAll(...);
Auth::user();
```

Stateless servisler için `new` mükemmeldir; stateful singleton'lar için statik facade. İkisi de okunabilir ve debug edilebilir.

---

## .env Yok

`.env` dosyaları yerine **PHP config dosyaları** kullanılır.

### Neden?

- `.env` string-bias'lı (her şey string, type coercion gerekir)
- IDE autocomplete `.env` için sınırlı
- OpCache PHP dosyalarını cache'ler, `.env` her request'te parse edilir
- `.env.example` vs `.env` ayrımı yerine, kritik dosyaları doğrudan `.gitignore`'a ekle

### Bunun yerine

```php
// config/database.php
return [
    'host' => '127.0.0.1',
    'database' => 'myapp',
    // ...
];

// .gitignore
config/database.php
config/services.php
```

---

## Tek Entry Point

Tüm istekler `index.php`'ye düşer (Apache `.htaccess` veya nginx `try_files`).

### Neden?

- Tek noktadan kontrol — bootstrap, error handling, analytics
- Standart Laravel/Symfony patterni
- `public/` web root değil — daha az dosya web sunucusunun ulaşabileceği yerde

### `index.php`'de neler oluyor

1. Sabitler (`ROOT_PATH`, `APP_PATH`, ...)
2. `php -S` için manuel asset serving
3. Composer autoload
4. Config preload (timezone, encoding)
5. ExceptionHandler kurulum
6. `App::run()`
7. `fastcgi_finish_request()` sonrası analytics

---

## Blade-lite (Compile Yok)

Laravel Blade'in syntax'ını **runtime regex** ile parse eder — compile cache yoktur.

### Neden?

- Setup yok, cache dizini yok, ısınma yok
- Küçük view'larda fark hissedilmez
- Production'da büyük perf gerekirse: Twig veya kendi compile katmanı eklenebilir

### Trade-off

- Her view her request'te parse edilir (ms-altı maliyet)
- `@include` derinliği fazlaysa az daha yavaş

---

## Active Record ORM

Obsidian ORM Active Record patternine uyar:

```php
$post = Post::find(1);
$post->title = 'Yeni';
$post->save();
```

### Alternatif: Data Mapper

Doctrine ORM gibi Data Mapper desteği yok. Daha karmaşık domain modelleri için manuel repository class yazabilirsin.

---

## Standart Komponentler

Framework dışına çıkmadan kullanılan kütüphaneler:

| Paket | Görev |
|---|---|
| `phpmailer/phpmailer` | SMTP mail |
| `symfony/uid` | UUID/ULID |
| `monolog/monolog` | Logging |
| `erusev/parsedown` | Markdown → HTML (docs için) |
| `psr/log` | PSR log interface'i |

> Toplam 4 doğrudan paket + transitive deps. Vendor dizini ~5MB.

---

**Sonraki:** [Helper Fonksiyonlar →](helpers.md)
