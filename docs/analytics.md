# Analytics (Trafik)

- [Giriş](#giris)
- [Veri Tabloları](#veri-tablolari)
- [Otomatik Loglama](#otomatik-loglama)
- [Custom Event Loglama](#custom-event-loglama)
- [Admin Dashboard](#admin-dashboard)
- [GA4 / GTM Entegrasyonu](#ga4-gtm-entegrasyonu)

---

## Giriş

IEF, dış servise (Google Analytics, Plausible) ihtiyaç duymadan kendi **first-party** ziyaretçi analitiğini sağlar. Tüm istekler `AnalyticsService` üzerinden DB'ye loglanır; cookie'ye dayanır ama IP anonimleştirilir.

> Sade ziyaretçi sayımı için yeterlidir. Daha karmaşık conversion funnel'ları için GA4'ü de ek olarak yapılandırabilirsin (Settings > SEO > GA4 ID).

---

## Veri Tabloları

3 tablo, normalize edilmiş:

### `traffic_sessions`

Bir ziyaretçinin oturumu (30 dk inaktivite sonrası yeni session). Her satır bir kullanıcı oturumu.

| Sütun | Açıklama |
|---|---|
| `id` | bigint pk |
| `visitor_id` | cookie'de tutulan UUID |
| `ip_hash` | IP'nin SHA-256 hash'i (GDPR) |
| `user_agent` | tam UA string |
| `device`, `browser`, `os` | parsed UA (kısa) |
| `country`, `city` | (varsa) geo lookup |
| `referrer` | ilk referer |
| `landing_page` | ilk girdiği path |
| `started_at`, `last_seen_at` | timestamps |

### `traffic_logs`

Her HTTP isteği. Detaylı log.

| Sütun |
|---|
| `session_id`, `method`, `path`, `query`, `status_code`, `duration_ms`, `created_at` |

### `traffic_events`

Custom event'ler (form submit, button click, e-ticaret olayı).

| Sütun |
|---|
| `session_id`, `event_name`, `payload` (JSON), `created_at` |

---

## Otomatik Loglama

`index.php` sonunda — response gönderildikten sonra arka planda:

```php
if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
try {
    (new AnalyticsService())->record(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
        http_response_code(),
        (int) round((microtime(true) - $_iefStart) * 1000)
    );
} catch (\Throwable $e) { /* sessiz */ }
```

Her istek:
1. Visitor ID cookie kontrolü (yoksa üretilir)
2. Mevcut session var mı (varsa `last_seen_at` güncelle, yoksa yenisini aç)
3. `traffic_logs` satırı eklenir
4. (varsa) custom event'ler eklenir

> Bu işlem `fastcgi_finish_request()` sonrası çalıştığı için response süresini etkilemez.

---

## Custom Event Loglama

### Backend

```php
use App\Services\AnalyticsService;

(new AnalyticsService())->event('contact_form_submit', [
    'subject' => $subject,
]);
```

### Frontend

```js
fetch('/admin/analytics/event', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name: 'cta_click', payload: { button: 'hero' } }),
});
```

> Frontend endpoint güvenliği: CSRF kontrolü yapılmaz çünkü public; rate limit ile koru.

---

## Admin Dashboard

**Admin > Trafik (Analytics)** — `/admin/analytics`

Sekmeler:

- **Genel** — son 7/30/90 gün ziyaretçi grafiği (ApexCharts area), top sayfalar, top referrer, device/browser breakdown
- **İstekler** — `/admin/analytics/requests` — tüm HTTP log'lar (DataTable, filtre)
- **Olaylar** — `/admin/analytics/events` — custom event'ler
- **Oturumlar** — `/admin/analytics/sessions` — session listesi
- **Oturum detayı** — `/admin/analytics/sessions/{id}` — bir ziyaretçinin tüm yolculuğu

---

## GA4 / GTM Entegrasyonu

Built-in analytics'in yanına GA4 veya GTM eklemek için:

**Admin > Ayarlar > SEO**:
- Google Analytics 4 ID → `G-XXXXXXXXXX`
- Google Tag Manager ID → `GTM-XXXXXXX`
- Facebook Pixel ID → `1234567890`

Layout otomatik enjekte eder. İki sistem paralel çalışır — overhead minimaldir.

---

**Sonraki:** [Blog & İçerik →](blog.md)
