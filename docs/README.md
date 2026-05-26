# IEF Framework — Dokümantasyon

> Modern, sıfır-konfigürasyonlu PHP MVC framework. `.env` yok, sadece çalışan kod.

**Sürüm:** 2.0.0
**PHP:** 8.1+
**Lisans:** MIT

---

## İçindekiler

### Prologue

- [Sürüm Notları](release-notes.md)
- [Yükseltme Rehberi](upgrade.md)
- [Katkıda Bulunma](contributing.md)

### Başlangıç

- [Kurulum](installation.md)
- [Konfigürasyon](configuration.md)
- [Dizin Yapısı](directory-structure.md)
- [Dağıtım (Deployment)](deployment.md)

### Mimari Kavramlar

- [Request Yaşam Döngüsü](lifecycle.md)
- [Service Container Yok — Neden?](architecture.md)
- [Helper Fonksiyonlar](helpers.md)

### Temeller

- [Routing](routing.md)
- [Middleware](middleware.md)
- [CSRF Koruması](csrf.md)
- [Controller'lar](controllers.md)
- [Request](requests.md)
- [Response](responses.md)
- [Views (Blade-lite)](views.md)
- [Validation](validation.md)
- [Hata Yönetimi](errors.md)
- [Logging](logging.md)

### Veritabanı

- [Veritabanı: Başlangıç](database.md)
- [Query Builder & PDO](queries.md)
- [Migration'lar](migrations.md)
- [Schema Builder](schema-builder.md)
- [Modeller (ORM)](models.md)
- [Seeder'lar](seeders.md)

### Güvenlik

- [Authentication](authentication.md)
- [Authorization (RBAC)](authorization.md)
- [Şifre Sıfırlama](password-reset.md)
- [Session](sessions.md)
- [Şifreleme](encryption.md)
- [Rate Limiting](rate-limiting.md)

### İletişim & Servisler

- [Mail (PHPMailer)](mail.md)
- [İletişim Formu](contact.md)
- [Randevu Sistemi](appointments.md)

### Frontend

- [Asset'ler & Tailwind](assets.md)
- [PWA](pwa.md)
- [SEO](seo.md)

### Admin Paneli

- [Genel Bakış](admin-panel.md)
- [Site Editör (Inline)](site-editor.md)
- [Ayarlar](settings.md)
- [Trafik Analytics](analytics.md)
- [Blog & İçerik](blog.md)
- [Medya Kütüphanesi](media.md)
- [Log Görüntüleyici](logs.md)
- [Kullanıcı Yönetimi](users.md)

### AI Entegrasyonu

- [Groq Cloud](ai-groq.md)
- [AI ile Blog Üretimi](blog-ai.md)

### CLI

- [./ief Komutları](cli.md)

### İleri Konular

- [Bakım Modu](maintenance.md)
- [Debug Bar](debug-bar.md)
- [Performans](performance.md)
- [Cache](cache.md)

---

## Hızlı Linkler

- 🌐 **Web:** [iefsoftware.tr](https://iefsoftware.tr)
- 🐙 **GitHub:** [byk1lla/IEFFrameWork](https://github.com/byk1lla/IEFFrameWork)
- 📦 **Packagist:** [iefsoftware/ief-framework](https://packagist.org/packages/iefsoftware/ief-framework)

## Felsefe

IEF Framework, Laravel'in zarafetini PHP'nin sade taraftarlarına getirmek için yazıldı. **3 prensip:**

1. **Sıfır konfigürasyon** — `.env` yok, container yok, sihirli auto-discovery yok. Her şey `config/` altında, okuyup düzenleyebileceğin PHP dizileri.
2. **Tek paket** — Routing, ORM, view engine, auth, migration, CLI, debug bar; hepsi 4 dış bağımlılıkla geliyor (`symfony/uid`, `phpmailer/phpmailer`, `psr/log`, `monolog/monolog`).
3. **Okunur kod** — Framework'ün kendi kaynağı bir hafta sonu okunup anlaşılabilecek büyüklükte. Sihir yok.

> "Karmaşık konfigürasyon yok, .env yok, sadece çalışan kod."
