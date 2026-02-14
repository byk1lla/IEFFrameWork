# 🦅 IEF Framework | Titan Global

> **Version:** 1.2.0 "Titan Global"  
> **Author:** IEF Software  
> **License:** MIT

## 🌟 Hakkında

**IEF Framework**, modern PHP uygulamaları geliştirmek için tasarlanmış, **hafif (lightweight)**, **ultra hızlı** ve **elit** bir MVC (Model-View-Controller) çatısıdır. V1.2.0 "Titan Global" sürümü ile birlikte, mimari stabilite ve üst düzey geliştirici deneyimi (Developer Experience) ön plana çıkarılmıştır.

Framework; gelişmiş yönlendirme (routing), **Obsidian ORM**, **Titan Guard** kimlik doğrulama, **Titan Pulse** görsel hata ayıklayıcı ve esnek middleware desteği ile gelir.

---

## 🚀 Öne Çıkan Özellikler

- **⚡ Titan Core Performance:** Gereksiz yüklerden arındırılmış, optimize edilmiş çekirdek yapı.
- **🛡️ Titan Guard (Auth):** Dahili, güvenli ve estetik kimlik doğrulama sistemi (Login/Register/Middleware).
- **📡 Titan Pulse (Debugger):** Glassmorphism tasarımlı, gerçek zamanlı SQL, Latency ve Memory takibi.
- **💾 Obsidian ORM:** Nesne tabanlı, "Lazy Building" destekli ve UUID/Auto-increment uyumlu veritabanı yönetimi.
- **🛣️ Titan Router:** RESTful rotalar, middleware gruplama ve akıllı enjeksiyon desteği.
- **💎 Premium Aesthetic:** Dark-mode odaklı, neon cyan vurgularla modern ve profesyonel arayüzler.

---

## 🛠️ Kurulum

### Gereksinimler
- PHP 8.1 veya üzeri
- Composer
- SQLite (Önerilen) veya MySQL/MariaDB

### Hızlı Başlangıç

1. **Projeyi Klonlayın:**
   ```bash
   git clone https://github.com/byk1lla/IEFFrameWork.git my-app
   cd my-app
   ```

2. **Bağımlılıkları Yükleyin:**
   ```bash
   composer install
   ```

3. **Veritabanı Hazırlığı:**
   Varsayılan olarak `database_v5.sqlite` kullanılır. Migrasyonları çalıştırmak için:
   ```bash
   ./ief migrate
   ```

4. **Sunucuyu Başlatın:**
   ```bash
   ./ief serve
   ```
   Tarayıcınızda `http://localhost:8000` adresine gidin. Pilot hesap: `nexus@core.id` / `matrix123`

---

## 📖 Mimari Yapı

### 1. Dizin Yapısı
```
/
├── app/
│   ├── Controllers/   # Business logic (AuthController, AdminController vb.)
│   ├── Core/          # Framework çekirdeği (Titan Core Engine)
│   ├── Models/        # Obsidian Modelleri
│   ├── Middleware/    # Titan Guard koruma katmanları
│   └── Helpers/       # Elit yardımcı araçlar
├── config/            # Yapılandırma matrisi (App, Database, Routes)
├── public/            # Web sunucusu giriş noktası ve assets
├── storage/           # Loglar ve SQLite veritabanı
├── vendor/            # Composer paketleri
├── ief                # Aether CLI aracı
└── index.php          # Global Matrix giriş noktası
```

### 2. Titan Guard & Güvenlik

Rotalarınızı tek bir satırla koruma altına alabilirsiniz:

```php
Router::get('/admin', 'AdminController@index', [
    'middleware' => \App\Middleware\AuthMiddleware::class
]);
```

---

## 🏗️ Katkıda Bulunma

1. Fork yapın.
2. Titan-branch oluşturun (`git checkout -b feature/titan-extension`).
3. Commit atın (`git commit -m 'Release: v1.2.0 build'`).
4. Push yapın ve Pull Request açın.

---

**IEF Framework** &copy; 2026 - Titan Global Edition.
