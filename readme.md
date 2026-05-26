# ief-framework

> **Sürüm:** 2.0.0 · **PHP:** 8.1+ · **Lisans:** MIT
> Modern, sıfır-konfigürasyonlu PHP MVC framework.
> [iefsoftware.tr](https://iefsoftware.tr) · [GitHub](https://github.com/byk1lla/IEFFrameWork) · [Packagist](https://packagist.org/packages/iefsoftware/ief-framework)

---

## 🚀 Tek Komutla Başla

```bash
composer create-project iefsoftware/ief-framework projem
cd projem
./ief migrate
./ief user:create
./ief serve
```

Tarayıcıdan `http://localhost:8000` — kurulum tamam.

---

## 📚 Tam Dokümantasyon

**[👉 Dokümantasyonu Aç](https://github.com/byk1lla/IEFFrameWork/tree/main/docs)** veya canlı sitende `/docs` adresinden eriş.

43+ konu başlığı, Laravel docs benzeri sidebar + arama + syntax highlight ile.

---

## ✨ Özellikler

| Modül | Açıklama |
|---|---|
| **Router** | RESTful + middleware grupları + closure desteği |
| **Obsidian ORM** | Active Record + Schema Builder DSL (MySQL/SQLite) |
| **Blade-lite** | `@extends`, `@section`, `@if`, `@foreach`, `@csrf`, `@php`, ... |
| **Titan Guard Auth** | Session-based + CSRF + role tabanlı + AuthMiddleware |
| **Site Editör** | **Canlı sayfa üzerinde tıkla-düzenle** — metin, görsel, ikon |
| **Tema Sidebar** | Editör'de logo/favicon/renk/font upload, canlı kayıt |
| **AI / Groq** | Blog yazısı AI ile oluştur (Llama 3.3 70B) |
| **Trafik Analytics** | First-party ziyaretçi/oturum/event tracking + ApexCharts |
| **Admin Paneli** | 7-sekme settings, mesaj/randevu/blog/medya/user yönetimi |
| **Bakım Modu** | Beyaz tema, brand renkli, animasyonlu 503 sayfası |
| **Mail (PHPMailer)** | `log` / `mail` / `smtp` driver |
| **Debug Bar** | SQL/route/latency/memory canlı gözlem |
| **CLI (`./ief`)** | `serve`, `migrate`, `make:*`, `user:create`, `route:list`, ... |
| **Docs Sitesi** | `/docs` — built-in dokümantasyon viewer (Parsedown + Prism) |

---

## 🎨 Frontend Stack

- **Landing:** Tailwind + Font Awesome + Poppins + HTMX + SweetAlert2 + GSAP + animate.css
- **Admin:** + ApexCharts + DataTables.js 2.x + Quill 2.0

CDN üzerinden geliyor — production'da `tailwindcss --minify` ile küçültebilirsin.

---

## 📐 Felsefe

1. **Sıfır konfigürasyon** — `.env` yok, container yok, magic auto-discovery yok
2. **Tek paket** — Routing/ORM/View/Auth/Migration/CLI hepsi içeride
3. **Okunur kod** — Framework çekirdeği bir hafta sonu okunup anlaşılır

> 4 dış bağımlılık: `phpmailer/phpmailer`, `symfony/uid`, `monolog/monolog`, `erusev/parsedown`. Vendor dizini ~5MB.

---

## 🛠 Geliştirme

```bash
./ief serve                # http://localhost:8000
./ief migrate              # Bekleyen migration'ları çalıştır
./ief migrate:fresh        # Sıfırdan kur (DİKKAT: tüm veriyi siler)
./ief make:controller PostController
./ief make:model Post
./ief make:migration create_posts_table
./ief make:middleware EnsureAdmin
./ief user:create          # Interaktif admin (şifre gizli)
./ief route:list           # Tüm route'ları tablo halinde
./ief cache:clear
./ief key:generate
./ief help
```

---

## 📂 Dizin Yapısı

```
app/
├── Controllers/        # HTTP controller'lar (Admin/ alt dizini)
├── Core/               # Framework çekirdeği (Router, Auth, View, ORM, ...)
├── Database/Migrations/
├── Helpers/            # Global fonksiyonlar
├── Middleware/
├── Models/
├── Services/           # MailService, GroqService, AnalyticsService
└── Views/              # Blade-lite template'ler

config/                 # app, database, mail, services, routes
docs/                   # 43 MD dosyası — /docs sayfasında render edilir
public/                 # asset'ler + uploads
storage/                # logs, cache
ief                     # CLI giriş noktası
index.php               # Tek HTTP entry point
```

---

## 🔐 Güvenlik

- Bcrypt şifre hashing (cost 10+)
- CSRF token tüm POST formlarda
- SQL injection: prepared statements her yerde
- XSS: `{{ }}` otomatik escape
- Honeypot bot koruması (formlarda)
- Rate limit (Settings > Güvenlik)
- HTTPS zorlama (Settings > Güvenlik)
- IP blocklist (CIDR destekli)
- Audit log: `storage/logs/app-YYYY-MM-DD.log`

---

## 🆕 v2.0.0 — Titan Global

**Yayın:** 24 Mayıs 2026

Major redesign + yeni özellik dalgası:

- Onur-style canlı inline editör + tema sidebar
- 7-sekme admin settings
- Modern bakım sayfası
- Groq AI entegrasyonu
- First-party trafik analytics
- Built-in dokümantasyon sitesi

> Detay: [Sürüm Notları](docs/release-notes.md) · [Yükseltme Rehberi](docs/upgrade.md)

---

## 🤝 Katkı

PR'ları açabilir, issue açabilirsin. Detay: [docs/contributing.md](docs/contributing.md).

---

## 📄 Lisans

MIT © 2026 [IEF Software](https://iefsoftware.tr)
