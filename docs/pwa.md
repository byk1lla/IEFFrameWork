# PWA (Progressive Web App)

- [Giriş](#giris)
- [Manifest](#manifest)
- [Service Worker](#service-worker)
- [Add to Home Screen](#add-to-home-screen)
- [İkonlar](#ikonlar)

---

## Giriş

IEF, varsayılan olarak temel **PWA** desteğiyle gelir — telefon "Ana Ekrana Ekle" yapabilir, app gibi açabilir. Tam offline desteği henüz built-in değil (service worker minimal).

---

## Manifest

`GET /manifest.webmanifest` — `PwaController@manifest`. JSON çıktısı:

```json
{
    "name": "Site Adı",
    "short_name": "Site",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#1d4ed8",
    "icons": [
        { "src": "/assets/img/icon-192.png", "sizes": "192x192", "type": "image/png" },
        { "src": "/assets/img/icon-512.png", "sizes": "512x512", "type": "image/png" }
    ]
}
```

Layout `<head>`'e link:

```html
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#1d4ed8">
```

İçerik dinamik — Settings > Genel > Site Adı + Settings > Görünüm > Primary Color.

---

## Service Worker

Şu an SW **devre dışı** — CSRF token cache sorununu önlemek için. Layout'ta eski SW kayıtlarını siler:

```js
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations()
        .then(rs => rs.forEach(r => r.unregister()));
}
```

İleride offline desteği için `sw.js` (proje kökünde) yazılabilir; Workbox bu iş için iyi.

---

## Add to Home Screen

iOS Safari + Android Chrome otomatik olarak "Ana Ekrana Ekle" önerir:

- Manifest tanımlı
- HTTPS (production)
- En az bir 192x192 ikon

İlk açıldığında splash screen + theme color uygulanır.

---

## İkonlar

`public/assets/img/` altında:
- `icon-192.png` (192x192)
- `icon-512.png` (512x512)
- `apple-touch-icon.png` (180x180, iOS için)
- `favicon.ico` (32x32)
- `favicon.svg` (vektör)

> [maskable.app](https://maskable.app) ile maskable icon test edebilirsin.

---

**Sonraki:** [Admin Paneli →](admin-panel.md)
