# Konfigürasyon

- [Felsefe: .env Yok](#felsefe-env-yok)
- [Konfigürasyon Dosyaları](#konfigrasyon-dosyalar)
- [config() Helper'ı](#config-helperi)
- [Ortam Bazlı Ayarlar](#ortam-bazl-ayarlar)
- [Dinamik Ayarlar (Settings)](#dinamik-ayarlar-settings)
- [App Key](#app-key)

---

## Felsefe: `.env` Yok

IEF Framework `.env` dosyası **kullanmaz**. Tüm konfigürasyon `config/` altındaki PHP dosyalarındadır. Neden?

- **Açıklık:** PHP dizisi PHP'cidir; `.env`'in string-bias'ından, type coercion sorunlarından kurtul
- **Versiyon kontrolü:** Production'a özel olan değerleri (DB şifresi, API key) sürüm kontrolünden hariç tutabilirsin (`.gitignore`); ama yapı dosyaları (`config/app.php`) git'te kalır
- **IDE desteği:** Tip ipuçları, autocomplete, "go to definition" çalışır
- **Performans:** OpCache cacheler, runtime parse maliyeti sıfır

API key gibi hassas alanlar `config/services.php`'de tutulur ve istenirse `.gitignore`'a eklenir.

---

## Konfigürasyon Dosyaları

```
config/
├── app.php          # uygulama adı, debug, locale, timezone, key
├── database.php     # MySQL/SQLite bağlantı
├── mail.php         # SMTP / mail() / log driver
├── routes.php       # route tanımları
└── services.php     # 3rd-party API key'leri (Groq vs.)
```

### `config/app.php`

```php
return [
    'name'        => 'IEF Framework',
    'version'     => '2.0.0',
    'env'         => 'production',
    'debug'       => false,
    'locale'      => 'tr',
    'timezone'    => 'Europe/Istanbul',
    'key'         => 'ief-32-byte-rastgele-anahtar',

    'maintenance' => [
        'enabled'      => false,
        'allowed_ips'  => ['127.0.0.1'],
        'message'      => 'Sistem bakımda.',
    ],
];
```

### `config/database.php`

```php
return [
    'driver'   => 'mysql',     // mysql | sqlite
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'ief_framework',
    'username' => 'ief_framework',
    'password' => 'gizli',
    'charset'  => 'utf8mb4',

    // SQLite kullanmak için:
    // 'driver' => 'sqlite',
    // 'path'   => __DIR__ . '/../storage/db.sqlite',
];
```

### `config/mail.php`

```php
return [
    'driver' => 'smtp',  // log | mail | smtp
    'from'   => [
        'address' => 'noreply@example.com',
        'name'    => 'Site Bildirimleri',
    ],
    'smtp' => [
        'host'       => 'smtp.example.com',
        'port'       => 587,
        'username'   => 'apikey',
        'password'   => 'gizli',
        'encryption' => 'tls',   // tls | ssl | ''
    ],
];
```

> Detay için: [Mail dokümantasyonu →](mail.md)

### `config/services.php`

```php
return [
    'groq' => [
        'api_key' => 'gsk_xxxxxxxxxxxxxxxxxxx',
        'model'   => 'llama-3.3-70b-versatile',
    ],
];
```

> `.gitignore`'a `config/services.php` eklemek mantıklı — repo'ya key sızmasın.

---

## `config()` Helper'ı

Tüm `config/` dizinindeki dosyalar boot anında yüklenir. Helper ile dot-notation:

```php
config('app.name');                 // "IEF Framework"
config('app.maintenance.enabled');  // false
config('mail.smtp.host');           // "smtp.example.com"

// İkinci parametre: default
config('app.does_not_exist', 'fallback');

// Runtime'da set (sadece o request boyunca):
config(['app.debug' => true]);
```

> Implementation: [app/Core/Config.php](../app/Core/Config.php)

---

## Ortam Bazlı Ayarlar

Birden çok ortam için dosyayı versiyonsuz tut:

```bash
# .gitignore
config/database.php
config/services.php
```

Ve örnekleri repo'da tut:

```bash
cp config/database.php config/database.php.example
git add config/database.php.example
```

Production sunucusunda gerçek dosyalar kalır; repo'daki `.example` template olur.

---

## Dinamik Ayarlar (Settings)

Sıkça değişen ayarlar (site adı, SEO meta, sosyal linkler, görünüm) **DB tabanlı** Settings sistemiyle yönetilir — admin panelden değiştirilir, kod değişmez.

```php
use App\Models\Setting;

$siteName = Setting::get('general.site_name', 'Default');
$logo     = Setting::get('appearance.logo_url');
$ga4      = Setting::get('seo.ga4_id');

// Grup olarak:
$general = Setting::group('general');  // ['site_name' => ..., 'maintenance' => ...]

// Yaz:
Setting::set('general.site_name', 'Yeni Adı', 'string');
```

| Grup | Yönetim |
|---|---|
| `general.*` | Admin > Ayarlar > Genel |
| `social.*` | Admin > Ayarlar > Sosyal |
| `appearance.*` | **Site Editör** sağ sidebar (önerilir) veya Admin > Ayarlar > Görünüm |
| `mail.*` | Admin > Ayarlar > Mail |
| `seo.*` | Admin > Ayarlar > SEO |
| `security.*` | Admin > Ayarlar > Güvenlik |
| `ai.*` | `config/services.php` (DB'de değil — güvenlik) |

> Detay: [Settings →](settings.md)

---

## App Key

`config/app.php`'deki `key` alanı; CSRF token üretimi, session signing, geleceğe yönelik encryption için kullanılır.

Yeni key üret:

```bash
./ief key:generate
# Yeni anahtar (config/app.php 'key' alanına yapıştır):
# ief-9f8e7d6c5b4a39281706152433423116a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2
```

Üretilen string'i `config/app.php`'deki `'key' => '...'` alanına yapıştır.

> **Production'da mutlaka değiştir** — default key güvensizdir.

---

**Sonraki:** [Dizin Yapısı →](directory-structure.md)
