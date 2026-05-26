# Kurulum

- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Web Kurulum Sihirbazı (Önerilen)](#web-kurulum-sihirbazi-onerilen)
- [Composer ile Kurulum](#composer-ile-kurulum)
- [Git ile Kurulum](#git-ile-kurulum)
- [Veritabanı Hazırlığı](#veritaban-hazrl)
- [İlk Çalıştırma](#ilk-altrma)
- [İlk Admin Kullanıcısı](#ilk-admin-kullancs)
- [Production Sunucu](#production-sunucu)
- [Shared Hosting (cPanel/Plesk)](#shared-hosting-cpanelplesk)

---

## Sistem Gereksinimleri

| Bileşen | Minimum | Önerilen |
|---|---|---|
| PHP | 8.1 | 8.3 |
| PDO + MySQL/MariaDB | ✓ | MySQL 8 / MariaDB 10.6 |
| PHP eklentileri | `mbstring`, `pdo_mysql`, `openssl`, `json` | + `intl`, `gd`, `curl` |
| Composer | 2.x | 2.7+ |
| Web sunucu | Apache 2.4 / nginx 1.20 / `php -S` | nginx + php-fpm |

---

## Web Kurulum Sihirbazı (Önerilen)

IEF, ilk çalıştırmada otomatik olarak **6-adımlı web sihirbazı** sunar — SSH/CLI gerekmez. Shared hosting (cPanel/Plesk) için ideal.

### Akış

`storage/installed.lock` dosyası yoksa, herhangi bir URL'i ziyaret ettiğinde otomatik `/install`'a yönlendirilirsin.

```
1. Sistem        → PHP versiyon, extension, klasör izinleri kontrolü
2. Veritabanı    → MySQL/SQLite credentials + canlı bağlantı testi
3. Migrasyon     → 16+ tablo otomatik oluşur (UI'da progress)
4. Yönetici      → İlk admin (superadmin) — bcrypt hash
5. Site Bilgisi  → Site adı/slogan/iletişim/dil/timezone (opsiyonel)
6. Tamam         → installed.lock yazılır, /install kapanır
```

Bittiğinde: `storage/installed.lock` oluşur, sihirbaz erişilemez hale gelir.

### Yeniden Çalıştırma

Sihirbazı tekrar açmak için:

```bash
./ief install:reset
```

veya manuel olarak `storage/installed.lock` dosyasını sil.

> **Güvenlik:** Sihirbaz lock yoksa **herkese** açık. Kurulumu hızlı tamamla; yarım kalmış bir sunucuyu unutulmuş şekilde bırakma.

### Adım Detayları

#### Adım 1 — Sistem

Kontrol edilenler:
- PHP 8.1+
- Extension: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `json`, `fileinfo`
- `vendor/autoload.php` var mı (composer install çalıştırılmış mı)
- Yazma izinleri: `storage/`, `storage/logs/`, `storage/cache/`, `storage/sessions/`, `public/uploads/`, `public/assets/img/content/`, `config/`

Sorun varsa kırmızı satır + öneri gösterilir. Çözüp "Yenile" yapacaksın.

#### Adım 2 — Veritabanı

MySQL veya SQLite seç. Form doldurduğunda **canlı bağlantı testi** yapılır — yanlış credentials veriyorsa kayıt yapılmaz, hata gösterilir. Başarılı olunca `config/database.php` otomatik yazılır.

> **cPanel'de DB oluşturmak:** Cpanel > MySQL Databases → boş DB oluştur, kullanıcı oluştur, kullanıcıyı DB'ye atayıp "All Privileges" ver.

#### Adım 3 — Migrasyon

"Başlat" butonuna basınca tüm migration'lar koşulur. UI'da konsol çıktısını görürsün. Hata olursa (yetki vb.) açıkça gösterilir.

#### Adım 4 — Yönetici

İsim + e-posta + şifre (min 8 karakter, bcrypt hash). Otomatik `superadmin` rolü.

#### Adım 5 — Site Bilgisi (Opsiyonel)

Site adı, slogan, iletişim e-posta/telefon, dil, timezone. "Atla" ile geçilebilir — sonra Admin > Ayarlar > Genel'den düzenlenir.

#### Adım 6 — Tamamlandı

Lock yazılır, sihirbaz kapanır. "Yönetime Giriş Yap" → `/login` → adım 4'te yarattığın hesapla gir.

---

### Composer Bağımlılıkları

Framework yalnızca **4 dış paket** kullanır:

```json
{
    "php": ">=8.1",
    "symfony/uid": "^7.0",
    "phpmailer/phpmailer": "^6.9",
    "psr/log": "^3.0",
    "monolog/monolog": "^3.5"
}
```

---

## Composer ile Kurulum

Yeni proje açmak için en hızlı yol:

```bash
composer create-project iefsoftware/ief-framework projem
cd projem
```

Bu komut, repo'yu klonlar ve `composer install` çalıştırır.

> **Not:** Default `config/database.php` **MySQL**'e ayarlıdır. SQLite ile başlamak istiyorsan `driver` alanını `sqlite` yap ve `path` ekle.

---

## Git ile Kurulum

```bash
git clone https://github.com/byk1lla/IEFFrameWork.git projem
cd projem
composer install
```

---

## Veritabanı Hazırlığı

`.env` kullanmıyoruz. DB bilgilerini doğrudan `config/database.php` içinde yönet:

```php
// config/database.php
return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'ief_framework',
    'username' => 'ief_framework',
    'password' => 'gizli_sifre',
    'charset'  => 'utf8mb4',
];
```

Boş DB oluştur:

```sql
CREATE DATABASE ief_framework CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ief_framework'@'localhost' IDENTIFIED BY 'gizli_sifre';
GRANT ALL ON ief_framework.* TO 'ief_framework'@'localhost';
FLUSH PRIVILEGES;
```

Migration'ları çalıştır:

```bash
./ief migrate
```

> 16+ tablo oluşur: `users`, `messages`, `media`, `posts`, `post_categories`, `settings`, `page_blocks`, `site_content`, `traffic_sessions`, `traffic_logs`, `traffic_events`, `appointments`, `appointment_services`, `password_resets`, vd.

---

## İlk Çalıştırma

```bash
./ief serve
# IEF Framework dev server: http://localhost:8000
```

Tarayıcıdan `http://localhost:8000` adresini aç — kurulum tamam.

---

## İlk Admin Kullanıcısı

İnteraktif komutla oluştur (şifre gizli istenir, terminal history'sine düşmez):

```bash
./ief user:create
```

```
İsim: Efe
E-posta: efe@example.com
Rol (superadmin|admin|editor|user) [superadmin]:
Şifre: ********
Şifre (tekrar): ********
✓ Kullanıcı oluşturuldu: efe@example.com (superadmin).
```

Ardından `/login` adresinden giriş yap.

> Roller: `superadmin` (her şey), `admin` (panel + içerik), `editor` (sadece içerik düzenleme), `user` (panel yok).

---

## Production Sunucu

### Apache (`.htaccess`)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /var/www/projem;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|webp|svg|ico|woff2?)$ {
        expires 30d;
        access_log off;
    }
}
```

### İzinler

```bash
chmod -R 775 storage sessions public/uploads public/assets/img/content
chown -R www-data:www-data storage sessions public/uploads
```

### Production checklist

- [ ] `config/app.php` → `'debug' => false`
- [ ] `config/database.php` → production DB bilgileri
- [ ] `./ief migrate` çalıştırıldı
- [ ] `./ief user:create` ile admin oluşturuldu
- [ ] HTTPS yönlendirmesi açık (Settings > Güvenlik > "HTTPS'i zorunlu kıl")
- [ ] Mail driver `smtp`'ye alındı (Settings > Mail)
- [ ] SEO meta tag'leri dolduruldu (Settings > SEO)
- [ ] `composer install --optimize-autoloader --no-dev`

---

## Shared Hosting (cPanel/Plesk)

SSH erişimi olmayan ortamlarda — sihirbazla 5 dakikada kurulur.

### 1. Yerel'de Composer Install

Sunucuda `composer` çoğu zaman yoktur. Yerelde bir kez çalıştır, vendor'ı dahil paket olarak yükle:

```bash
# Yerel makinende
git clone https://github.com/byk1lla/IEFFrameWork projem
cd projem
composer install --no-dev --optimize-autoloader
zip -r projem.zip . -x ".git/*" "node_modules/*" "storage/database.sqlite" "config/database.php" "config/services.php"
```

### 2. cPanel'e Yükle

1. **cPanel > File Manager**
2. `public_html/` dizinine `projem.zip`'i yükle, sağ tık > Extract
3. (Önerilen) `public_html/projem` yerine doğrudan `public_html/` altında dosyalar — başka site yoksa
4. `chmod 775` izinleri uygula (File Manager > klasör seç > Permissions):
   - `storage/`, `storage/logs/`, `storage/cache/`, `storage/sessions/`
   - `public/uploads/`, `public/assets/img/content/`
   - `config/` (sihirbaz `config/database.php` yazacak)

### 3. cPanel'de Veritabanı Oluştur

1. **cPanel > MySQL Databases**
2. **Create New Database:** `kullanici_iefdb` gibi (prefix otomatik gelir)
3. **MySQL Users:** yeni kullanıcı oluştur — şifreyi not al
4. **Add User To Database:** kullanıcıyı az önce oluşturulan DB'ye ekle, **All Privileges** ver

### 4. Tarayıcıdan Aç

```
https://example.com/install
```

6-adımlı sihirbaz açılır:
- DB adımında host = `localhost`, db/username/password = cPanel'de oluşturduğun değerler
- Bağlantı testi başarılıysa Devam → migrate → admin → site → bitti

### 5. .htaccess

Çoğu cPanel'de gelir; gelmediyse `public_html/.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

### 6. HTTPS

cPanel > **AutoSSL** veya **Let's Encrypt** ile sertifika al — otomatik yenilenir.

### Yaygın Sorunlar

| Hata | Çözüm |
|---|---|
| "klasör yazılamıyor" | `chmod 775` uygula File Manager'dan |
| 500 Internal Server Error | `storage/logs/app-YYYY-MM-DD.log` oku; PHP versiyonu 8.1+ olduğundan emin ol (Cpanel > MultiPHP) |
| MySQL bağlantı reddedildi | Host genelde `localhost`; cPanel'de DB ön-eki (`kullanici_`) ile birlikte gir |
| `/install` 404 | `.htaccess` eksik veya mod_rewrite kapalı |

---

**Sonraki:** [Konfigürasyon →](configuration.md)
