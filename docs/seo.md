# SEO

- [Giriş](#giris)
- [Meta Etiketleri](#meta-etiketleri)
- [OG / Twitter Card](#og-twitter-card)
- [Canonical](#canonical)
- [Sitemap.xml](#sitemapxml)
- [Robots.txt](#robotstxt)
- [Analytics / Tracking](#analytics-tracking)
- [Search Console Doğrulama](#search-console-dogrulama)

---

## Giriş

IEF, SEO best practice'lerini Admin > Ayarlar > SEO sekmesinden yönetilebilir hale getirir. Yapılandırma kod değişikliği gerektirmez.

---

## Meta Etiketleri

Layout `<head>` otomatik enjekte eder:

```html
<title>Sayfa Başlığı · Site Adı</title>
<meta name="description" content="...">
<meta name="keywords" content="...">
```

Kaynak: Settings > SEO > Meta Title / Description / Keywords.

Sayfa-bazlı override (controller'dan):

```php
return $this->view('blog.show', [
    'post' => $post,
    'seo_title'       => $post->meta_title ?: $post->title,
    'seo_description' => $post->meta_description ?: $post->excerpt,
]);
```

---

## OG / Twitter Card

```html
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="...">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
```

Default OG image: Settings > Görünüm > Varsayılan OG Görsel.
Blog yazısı varsa: post.og_image.

Boyut: **1200x630** önerilir (Facebook/LinkedIn için optimal).

---

## Canonical

Settings > SEO > Canonical Base URL doluysa her sayfaya canonical eklenir:

```html
<link rel="canonical" href="https://example.com/blog/abc">
```

---

## Sitemap.xml

`GET /sitemap.xml` — SeoController@sitemap. Otomatik olarak:

- Statik sayfalar (/, /blog, /iletisim, /randevu)
- Yayınlanmış blog yazıları (her birinin `published_at`'i ile)

Settings > SEO > "Sitemap.xml otomatik oluştur" toggle ile devre dışı bırakılabilir.

Google Search Console'a şunu ekle: `https://example.com/sitemap.xml`.

---

## Robots.txt

`GET /robots.txt` — Settings > SEO > robots.txt İçeriği alanından okunur.

Default:

```
User-agent: *
Allow: /
Sitemap: https://example.com/sitemap.xml
```

Belirli path'leri engellemek için:

```
User-agent: *
Disallow: /admin/
Disallow: /login
Allow: /
```

---

## Analytics / Tracking

Settings > SEO içinden:

- **Google Analytics 4** — `G-XXXXXXXXXX` → otomatik `gtag.js` enjekte
- **Google Tag Manager** — `GTM-XXXXXXX` → GTM container snippet
- **Facebook Pixel** — `1234567890` → fbq init

Layout `<head>` ve `<body>` başlangıcına otomatik enjekte edilir.

---

## Search Console Doğrulama

Settings > SEO:

- **Google Site Verification** — meta tag content (HTML metoduyla doğrulama)
- **Bing Site Verification** — aynı şekilde

Layout `<head>`'e otomatik enjekte:

```html
<meta name="google-site-verification" content="abc...xyz">
<meta name="msvalidate.01" content="ABC...XYZ">
```

---

**Sonraki:** [Asset'ler →](assets.md)
