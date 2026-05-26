# Kurulum

- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Composer ile Kurulum](#composer-ile-kurulum)
- [Git ile Kurulum](#git-ile-kurulum)
- [Veritabanı Hazırlığı](#veritaban-hazrl)
- [İlk Çalıştırma](#ilk-altrma)
- [İlk Admin Kullanıcısı](#ilk-admin-kullancs)
- [Production Sunucu](#production-sunucu)

---

## Sistem Gereksinimleri

| Bileşen | Minimum | Önerilen |
|---|---|---|
| PHP | 8.1 | 8.3 |
| PDO + MySQL/MariaDB | ✓ | MySQL 8 / MariaDB 10.6 |
| PHP eklentileri | `mbstring`, `pdo_mysql`, `openssl`, `json` | + `intl`, `gd`, `curl` |
| Composer | 2.x | 2.7+ |
| Web sunucu | Apache 2.4 / nginx 1.20 / `php -S` | nginx + php-fpm |

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

**Sonraki:** [Konfigürasyon →](configuration.md)
