# Sürüm Notları

- [v2.0.0 — Titan Global](#v200--titan-global)
- [v1.x — Legacy](#v1x--legacy)

---

## v2.0.0 — Titan Global

**Yayın:** 24 Mayıs 2026

Major redesign + yeni özellik dalgası. Önceki sürümlerle birebir uyumsuzdur — `migrate:fresh` ile sıfırdan başlamak önerilir.

### ✨ Yeni

- **Site Editör (Inline Editing)** — `/site-editor` ile canlı sayfa düzenleme: metin/görsel/ikon tıkla-düzenle
- **Tema Sidebar** — editör'de sağdan açılan görünüm paneli (logo, favicon, renk, font, footer text upload + canlı kayıt)
- **Onur-style Auto-Scanner** — view'larda özel kod olmadan tüm metin/görsel düzenlenebilir
- **Admin Settings — 7 sekme:** Genel, Sosyal, Görünüm, Mail, SEO, Güvenlik, AI
- **Modern Bakım Sayfası** — beyaz tema, brand renk, animasyonlu çark, ETA + iletişim
- **AI / Groq Entegrasyonu** — blog yazısı AI ile oluşturma (Llama 3.3 70B)
- **Trafik Analytics** — first-party ziyaretçi/oturum/event tracking, ApexCharts grafikleri
- **Dokümantasyon Sitesi** — `/docs` route'u, Laravel docs benzeri sidebar + syntax highlight
- **CLI `./ief user:create`** — interaktif admin oluşturma (şifre gizli)
- **PHPMailer Entegrasyonu** — log/mail/smtp driver

### 🔧 Çekirdek Değişiklikler

- Tüm controller'lar `App\Core\Controller` base class'ından kalıtır
- Schema Builder DSL (Blueprint pattern)
- MySQL **default driver** oldu (önceden SQLite)
- `.env` kaldırıldı, tüm config `config/*.php`'ye taşındı
- Session-based site editor toggle (`/site-editor` → `/site-editor/cikis`)
- Maintenance bypass: admin login, `/admin/*`, `/docs/*`, `/login*`, `/sifre-sifirla*`

### 📦 Bağımlılıklar

- `phpmailer/phpmailer ^6.9` (yeni)
- `symfony/uid ^7.0`
- `monolog/monolog ^3.5`
- `erusev/parsedown ^1.8` (yeni — docs için)

### ⚠ Breaking Changes

- Eski `ErrorReporterController`, `ExampleController`, `TaskController` kaldırıldı
- `app/Database/Migrations/m0001_initial_setup.php` ve eski migration'lar yenilendi
- `app/Views/components/*` (alert, button, card partial'lar) kaldırıldı — Tailwind utility'ler kullanılıyor
- View engine'i: yeni `@php`, `@isset`, `@empty`, `@forelse` direktifleri eklendi

---

## v1.x — Legacy

Önceki sürümler hakkında bilgi için git history'e bakın:

```bash
git log --oneline v1.0.0..v1.2.0
```

> v1.x uzun süredir desteklenmiyor. Yeni projeler için v2'yi kullan.

---

**Sonraki:** [Yükseltme Rehberi →](upgrade.md)
