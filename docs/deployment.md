# Dağıtım (Deployment)

- [Giriş](#giris)
- [Hosting Tipi](#hosting-tipi)
- [VPS / Dedicated Sunucu](#vps-dedicated-sunucu)
- [Shared Hosting](#shared-hosting)
- [Çevresel Konfigürasyon](#cevresel-konfigurasyon)
- [Production Checklist](#production-checklist)

---

## Giriş

IEF, modern PHP standartlarına uyduğu için **çoğu hosting** üzerinde sorunsuz çalışır. Bu sayfa farklı hosting tiplerine göre setup rehberi sunar.

---

## Hosting Tipi

| Tip | Uygun mu? | Notlar |
|---|---|---|
| **VPS / Dedicated** (DigitalOcean, Hetzner, AWS EC2) | ✓✓ İdeal | Tam kontrol, nginx + php-fpm önerilir |
| **Shared Hosting** (cPanel) | ✓ Çalışır | `.htaccess` ile |
| **Platform-as-a-Service** (Laravel Forge, Vapor) | ✓ Çalışır | Laravel için optimize ama IEF de çalışır |
| **Serverless** (Lambda) | ✗ Önerilmez | Session/storage dosya tabanlı |

---

## VPS / Dedicated Sunucu

### 1. PHP 8.3 + Extensions

```bash
sudo apt install php8.3-fpm php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-gd php8.3-zip php8.3-intl \
    nginx mysql-server unzip
```

### 2. Proje Klonla

```bash
cd /var/www
git clone https://github.com/byk1lla/IEFFrameWork projem
cd projem
composer install --optimize-autoloader --no-dev
```

### 3. nginx Config

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
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
        try_files $uri =404;
    }

    # Block sensitive
    location ~ /\.(env|git) { deny all; }
    location ~ ^/(storage|sessions|app|config|vendor)/ { deny all; }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/projem /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 4. HTTPS (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d example.com -d www.example.com
```

### 5. Permissions

```bash
sudo chown -R www-data:www-data /var/www/projem
sudo chmod -R 775 storage sessions public/uploads public/assets/img/content
```

### 6. Setup

```bash
# config dosyalarını düzenle
nano config/database.php
nano config/app.php   # debug => false

# Migration
./ief migrate

# İlk admin
./ief user:create
```

---

## Shared Hosting

cPanel/Plesk:

1. Composer install yerine repo'nun composer'sız bir paketini yükle (vendor klasörü dahil)
2. Dosyaları `public_html/` altına çıkar (kök = `index.php`)
3. `.htaccess` zaten gelir — Apache mod_rewrite açık olmalı
4. cPanel > MySQL > DB + User oluştur, `config/database.php` doldur
5. cPanel > PHP Selector > PHP 8.1+ seç + extensions açık
6. cPanel > Terminal varsa: `./ief migrate && ./ief user:create`
7. Yoksa: phpMyAdmin'den `app/Database/Migrations/*.php` dosyalarındaki SQL'leri manuel çalıştır

---

## Çevresel Konfigürasyon

`config/` dosyalarını ortama göre ayır:

```bash
# .gitignore
config/database.php       # production şifreleri
config/services.php       # API key'ler

# Repo'da kalan:
config/database.php.example
config/services.php.example
```

Deploy sırasında `cp .example`'dan kopyala, gerçek değerleri doldur.

---

## Production Checklist

- [ ] `config/app.php` → `debug: false`, `env: 'production'`
- [ ] `config/database.php` → production DB
- [ ] `config/services.php` → gerçek Groq key (varsa)
- [ ] HTTPS yönlendirmesi (nginx 301)
- [ ] `Settings > Mail > Driver` → `smtp`
- [ ] `Settings > Genel > Bakım Modu` → kapalı
- [ ] `Settings > SEO` → meta + GA4 + sitemap
- [ ] `Settings > Güvenlik > HTTPS Zorunlu` → açık
- [ ] OpCache açık + tuned (`opcache.validate_timestamps=0`)
- [ ] `chmod 775 storage sessions public/uploads`
- [ ] `chown www-data:www-data` (sunucu kullanıcısı)
- [ ] Cron: log rotation, eski sessions temizleme
- [ ] Backup: günlük DB + dosya backup
- [ ] Monitoring: uptime ping, error log alarm

---

**Sonraki:** [Mimari →](architecture.md)
