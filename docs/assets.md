# Asset'ler & Tailwind

- [Giriş](#giris)
- [Tech Stack](#tech-stack)
- [CDN vs Build](#cdn-vs-build)
- [Tailwind CDN](#tailwind-cdn)
- [Production Build](#production-build)
- [Asset Yapısı](#asset-yapisi)

---

## Giriş

IEF, modern bir frontend stack'i hazır gelir: **Tailwind + Font Awesome + Poppins + HTMX + SweetAlert2 + GSAP + animate.css**. Admin paneli üstüne **ApexCharts + DataTables 2.x** ekler.

---

## Tech Stack

### Landing

| Tool | Versiyon | CDN |
|---|---|---|
| Tailwind CSS | 3.x | `cdn.tailwindcss.com` |
| Font Awesome | 6.5.2 | cdnjs |
| Poppins | Google Fonts | fonts.googleapis.com |
| HTMX | 2.0.4 | unpkg |
| SweetAlert2 | 11 | jsdelivr |
| GSAP + ScrollTrigger | 3.12.5 | cdnjs |
| animate.css | 4.1.1 | jsdelivr |

### Admin

Yukarıdakilere ek olarak:

| Tool | Versiyon |
|---|---|
| ApexCharts | 3.x |
| DataTables.js | 2.x |
| Quill 2.0 (blog editör) | 2.x |

---

## CDN vs Build

Default: tüm asset'ler **CDN**'den yüklenir.

| Pro | Con |
|---|---|
| Setup yok, build adımı yok | Internet bağımlı |
| Tarayıcı cache'i paylaşımlı | İlk yüklenme +50-100ms |
| Boyut endişesi yok | Self-host edilmesi gerekebilir (GDPR/uptime) |

Production'da kendi domain'inden serve etmek istersen → aşağıdaki **Production Build**.

---

## Tailwind CDN

Layout'ta:

```html
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Poppins', 'system-ui', 'sans-serif'] },
                colors: {
                    brand: { 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' },
                    accent: { 500: '#06b6d4' },
                },
            }
        }
    };
</script>
```

> CDN modu development için ideal — config inline değiştirilebilir, build yok. Production'da tüm utility class'lar JS bundle'a dahil edildiği için bundle büyür (~400KB gzipped).

---

## Production Build

Boyutu küçültmek için Tailwind CLI ile sadece kullanılan class'ları topla:

### 1. Kurulum

```bash
npm init -y
npm install -D tailwindcss
npx tailwindcss init
```

### 2. `tailwind.config.js`

```js
module.exports = {
    content: [
        './app/Views/**/*.php',
        './public/assets/js/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: { 600: '#2563eb', 700: '#1d4ed8' },
            }
        }
    },
}
```

### 3. Input CSS

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 4. Build

```bash
npx tailwindcss -i resources/css/app.css -o public/assets/css/app.css --minify
```

### 5. Layout'u Güncelle

`<script src="https://cdn.tailwindcss.com">` yerine:

```html
<link rel="stylesheet" href="/assets/css/app.css">
```

Sonuç: ~20-30KB gzipped CSS (sadece kullanılan utility'ler).

---

## Asset Yapısı

```
public/assets/
├── css/
│   ├── editor.css     # Inline editör (osk-*)
│   └── app.css        # (build sonrası) Tailwind
├── js/
│   └── editor.js      # Inline editör
└── img/
    ├── content/       # Editör'den yüklenen görseller
    ├── logo.svg
    └── og.png
```

Upload klasörü:

```
public/uploads/
└── 2026/05/...        # tarih bazlı klasörler
```

---

**Sonraki:** [PWA →](pwa.md)
