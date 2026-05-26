# Ayarlar

- [Giriş](#giris)
- [Setting API](#setting-api)
- [7 Sekme](#7-sekme)
    - [Genel](#genel)
    - [Sosyal](#sosyal)
    - [Görünüm](#gorunum)
    - [Mail](#mail)
    - [SEO](#seo)
    - [Güvenlik](#guvenlik)
    - [AI](#ai)
- [Ayar Okuma Helper'ları](#ayar-okuma-helperlari)

---

## Giriş

Ayarlar, kod değişikliği gerektirmeden yönetilen, DB tabanlı (`settings` tablosu) site konfigürasyonlarıdır. Sık değişen değerler (site adı, SEO meta, mail driver, görünüm renkleri) burada yaşar.

Admin > Ayarlar — `/admin/settings`

---

## Setting API

```php
use App\Models\Setting;

// Oku
$siteName = Setting::get('general.site_name', 'Default');

// Yaz
Setting::set('general.site_name', 'Yeni Adı', 'string');

// Grup oku
$general = Setting::group('general');
// ['site_name' => '...', 'contact_email' => '...', ...]

// Sil
Setting::forget('general.maintenance');
```

Anahtarlar dot-notation: `<grup>.<alan>` (örn. `seo.meta_title`).

Tip parametresi: `string`, `int`, `bool`, `json`.

---

## 7 Sekme

### Genel

```
site_name, site_tagline, contact_email, contact_phone, address,
whatsapp_number, timezone, locale, per_page,
maintenance, maintenance_message, maintenance_eta, debug_bar
```

- `whatsapp_number` boş değilse sağ alt floating WhatsApp butonu görünür
- `maintenance` true → 503 + bakım sayfası (admin bypass)
- `debug_bar` toggle ile alt çubuk gösterilir/gizlenir
- `timezone`, `locale`, `per_page` — sırasıyla zaman dilimi, dil, liste sayfası boyutu

### Sosyal

Footer'da link olarak gösterilen sosyal medya hesapları:

```
facebook, instagram, twitter, linkedin, youtube, github, tiktok,
show_in_footer (bool)
```

Her birinin yanında platform ikonu + renk preview.

### Görünüm

> **Tip:** Bu ayarlar canlı önizlemeli olarak **Site Editör** sağ sidebar'ından da yönetilebilir. Form bu sekmede `<details>` altında gizlidir.

```
logo_url, logo_dark_url, favicon_url, og_default_url,
primary_color, accent_color, font_family,
footer_text, copyright_text
```

Renk seçimi color picker + hex input. Font dropdown'ı Google Fonts'tan otomatik yükler.

### Mail

```
driver (log|mail|smtp),
from_address, from_name, reply_to, admin_inbox,
smtp_host, smtp_port, smtp_user, smtp_pass, smtp_encryption
```

`log` — dev için, mail göndermez sadece dosyaya yazar
`smtp` — production önerilir

> Detay: [Mail →](mail.md)

### SEO

```
meta_title, meta_description, meta_keywords,
og_image, canonical_base,
ga4_id, gtm_id, fb_pixel_id,
google_verification, bing_verification,
robots, sitemap_enabled
```

GA4/GTM/Pixel ID'leri girilince layout'a otomatik enjekte edilir.

### Güvenlik

```
rate_limit_per_hour, max_login_attempts, lockout_minutes,
session_lifetime_min, csrf_lifetime_min,
honeypot_enabled, force_https, allow_registration,
blocked_ips
```

`blocked_ips` — her satıra bir IP veya CIDR (örn. `10.0.0.0/8`).

### AI

Sadece **bilgi sekmesi** — form yok. Groq API key güvenlik için `config/services.php`'de manuel yapılandırılır:

```php
// config/services.php
return [
    'groq' => [
        'api_key' => 'gsk_xxxxxxxxxxxx',
        'model'   => 'llama-3.3-70b-versatile',
    ],
];
```

Key girilince Admin > Ayarlar > AI'da yeşil "Aktif" badge'i görünür ve Blog > "AI ile Oluştur" butonu açılır.

> Detay: [AI / Groq →](ai-groq.md)

---

## Ayar Okuma Helper'ları

View'da kısa kullanım:

```blade
@php
    $siteName = \App\Models\Setting::get('general.site_name', config('app.name'));
    $logo     = \App\Models\Setting::get('appearance.logo_url');
@endphp

<a href="/">
    @if($logo)
        <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-8">
    @else
        {{ $siteName }}
    @endif
</a>
```

---

**Sonraki:** [Analytics →](analytics.md)
