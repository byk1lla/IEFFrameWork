# Dizin Yapısı

```
ief-framework/
├── app/                       # Uygulama kodu
│   ├── Controllers/
│   │   ├── Admin/             # Admin paneli controller'ları
│   │   │   ├── AnalyticsController.php
│   │   │   ├── AppointmentController.php
│   │   │   ├── BlogController.php
│   │   │   ├── ContentEditorController.php   # Inline editör save/upload
│   │   │   ├── EditorController.php          # Eski block editörü
│   │   │   ├── LogController.php
│   │   │   ├── MediaController.php
│   │   │   ├── MessageController.php
│   │   │   ├── SettingsController.php
│   │   │   └── UserController.php
│   │   ├── AdminController.php
│   │   ├── AppointmentController.php
│   │   ├── AuthController.php
│   │   ├── BlogController.php
│   │   ├── ContactController.php
│   │   ├── PwaController.php
│   │   ├── SeoController.php
│   │   └── WelcomeController.php
│   │
│   ├── Core/                  # Framework çekirdeği — dokunmadan kullanılır
│   │   ├── App.php            # Bootstrap
│   │   ├── Auth.php           # Kimlik doğrulama (Titan Guard)
│   │   ├── Blueprint.php      # Schema Builder DSL
│   │   ├── Config.php         # Konfig yükleyici
│   │   ├── Controller.php     # Base controller
│   │   ├── Database.php       # PDO singleton + helper'lar
│   │   ├── DebugBar.php       # Alt çubuk (Titan Pulse)
│   │   ├── ExceptionHandler.php
│   │   ├── Lang.php           # Çoklu dil
│   │   ├── Logger.php         # Monolog wrapper
│   │   ├── MigrationRunner.php
│   │   ├── Model.php          # Base ORM (Obsidian)
│   │   ├── Request.php
│   │   ├── Resource.php
│   │   ├── Response.php
│   │   ├── Router.php
│   │   ├── Schema.php         # Schema::create() facade
│   │   ├── Session.php
│   │   ├── SiteContent.php    # Inline editör content store
│   │   ├── Storage.php        # Dosya yükleme/silme
│   │   ├── Validator.php
│   │   └── View.php           # Blade-lite engine
│   │
│   ├── Database/
│   │   ├── Migrations/        # Schema migration'ları
│   │   └── Seeders/           # (varsa) seed sınıfları
│   │
│   ├── Helpers/
│   │   └── helpers.php        # Global fonksiyonlar (e, redirect, config, content, ...)
│   │
│   ├── Middleware/
│   │   └── AuthMiddleware.php
│   │
│   ├── Models/
│   │   ├── Appointment.php
│   │   ├── AppointmentService.php
│   │   ├── Media.php
│   │   ├── Message.php
│   │   ├── PageBlock.php
│   │   ├── Post.php
│   │   ├── PostCategory.php
│   │   ├── Setting.php
│   │   ├── TrafficSession.php
│   │   ├── TrafficLog.php
│   │   ├── TrafficEvent.php
│   │   └── User.php
│   │
│   ├── Services/
│   │   ├── AnalyticsService.php
│   │   ├── GroqService.php
│   │   └── MailService.php
│   │
│   └── Views/                 # Tüm template'ler
│       ├── layouts/           # app, admin, guest
│       ├── admin/             # Admin paneli sayfaları
│       │   ├── analytics/
│       │   ├── appointments/
│       │   ├── blog/
│       │   ├── editor/        # Site editör panelleri
│       │   ├── logs/
│       │   ├── media/
│       │   ├── messages/
│       │   ├── settings/
│       │   └── users/
│       ├── auth/              # login, sifre-sifirla
│       ├── blog/              # Blog frontend
│       ├── contact/
│       ├── errors/            # 404, 500, maintenance
│       └── welcome.php
│
├── config/                    # Konfigürasyon dosyaları
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   ├── routes.php
│   └── services.php
│
├── public/                    # Web root (asset'ler ve uploads)
│   ├── assets/
│   │   ├── css/
│   │   │   └── editor.css     # Inline editör CSS
│   │   ├── js/
│   │   │   └── editor.js      # Inline editör JS
│   │   └── img/
│   │       └── content/       # Editör'den yüklenen görseller
│   ├── uploads/
│   └── favicon.ico
│
├── resources/
│   └── lang/                  # Dil dosyaları
│       ├── tr.php
│       └── en.php
│
├── storage/                   # Çalışma zamanı verisi (gitignore)
│   ├── cache/
│   └── logs/
│       ├── app-YYYY-MM-DD.log
│       └── mail-YYYY-MM-DD.log
│
├── sessions/                  # PHP session dosyaları (gitignore)
│
├── docs/                      # Bu dokümantasyon
│
├── vendor/                    # Composer (gitignore)
│
├── ief                        # CLI giriş noktası
├── index.php                  # Tek HTTP entry point
├── composer.json
└── readme.md
```

## Notlar

- **`public/` web root değil** — `index.php` proje kökündedir. Apache `.htaccess` veya nginx `try_files` ile her istek `index.php`'ye düşer. `php -S` server'ında `index.php` içindeki bir snippet `public/assets/*`, `public/uploads/*` yollarını manuel servis eder.
- **`storage/` ve `sessions/` write-edilebilir olmalı** (production: `chmod 775`, `www-data` ownership).
- **`app/Database/Migrations/`** dosya adları zaman damgalı: `m20260524_000001_create_users_table.php`. Çalıştırma sırası dosya adı sırasıdır.
- **`app/Core/`** dosyalarına dokunmak gerekmez — framework çekirdeği. Genişletmek için `app/Services/` veya `app/Helpers/` kullan.

---

**Sonraki:** [Request Yaşam Döngüsü →](lifecycle.md)
