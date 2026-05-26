# CLI (./ief)

- [Giriş](#giris)
- [Kullanım](#kullanim)
- [Sunucu](#sunucu)
- [Migration](#migration)
- [Generator'lar](#generators)
- [Kullanıcı Yönetimi](#kullanici-yonetimi)
- [Route & Cache](#route-cache)
- [Tüm Komutlar](#tum-komutlar)

---

## Giriş

`ief` script'i proje kökündeki CLI giriş noktasıdır. Tüm framework komutları buradan çalıştırılır.

```bash
./ief --help
```

> Eğer `./ief: Permission denied` hatası alırsan: `chmod +x ief`.

---

## Kullanım

```bash
./ief <komut> [argümanlar]
```

---

## Sunucu

```bash
./ief serve              # http://localhost:8000
./ief serve 9000         # http://localhost:9000
./ief serve 0.0.0.0:8000 # tüm interface'lerde dinle (LAN test)
```

PHP'nin built-in dev server'ını başlatır; `index.php` üzerinden tüm istekleri yönlendirir.

> **Production'da kullanma** — built-in server tek thread'li ve dev odaklı. Production için nginx + php-fpm.

---

## Migration

```bash
./ief migrate                  # Bekleyen migration'ları çalıştır
./ief migrate:rollback         # Son batch'i geri al
./ief migrate:rollback 3       # Son 3 batch'i geri al
./ief migrate:fresh            # TÜM tabloları sil + sıfırdan migrate (DİKKAT)
./ief migrate:status           # Durumu tablo halinde göster
```

> Detay: [Migration'lar →](migrations.md)

---

## Generator'lar

```bash
./ief make:controller PostController
./ief make:controller Admin/ReportController     # Admin alt dizinine

./ief make:model Post

./ief make:migration create_posts_table
# → app/Database/Migrations/m<YYYYMMDD>_<HHMMSS>_create_posts_table.php

./ief make:middleware EnsureAdmin
# → app/Middleware/EnsureAdminMiddleware.php (suffix otomatik)

./ief make:seeder DefaultRolesSeeder
# → app/Database/Seeders/DefaultRolesSeeder.php
```

Stub'lar minimal — düzenleyip kullan.

---

## Kullanıcı Yönetimi

İnteraktif kullanıcı oluşturma. Şifre gizli girilir (terminal history'sine düşmez):

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

E-posta zaten varsa update modu sorar.

---

## Route & Cache

```bash
./ief route:list           # Tüm tanımlı route'ları tablo halinde
./ief cache:clear          # storage/cache temizle
./ief key:generate         # Yeni 32-byte app key üret (manuel yapıştır)
```

---

## Tüm Komutlar

```
serve [port]              Geliştirme sunucusunu başlat (default :8000)
migrate                   Bekleyen migration'ları çalıştır
migrate:rollback [steps]  Son migration'ları geri al (default 1)
migrate:fresh             Tüm tabloları sil + sıfırdan migrate et
migrate:status            Migration durumunu göster
make:controller <Name>    Yeni controller oluştur
make:model <Name>         Yeni model oluştur
make:migration <desc>     Yeni migration oluştur
make:middleware <Name>    Yeni middleware oluştur
make:seeder <Name>        Yeni seeder oluştur
db:seed                   Tüm seeder'ları çalıştır
user:create               Interaktif kullanıcı oluştur (şifre gizli)
route:list                Tanımlı tüm route'ları listele
cache:clear               storage/cache dizinini temizle
key:generate              Yeni app key üret (manuel yapıştır)
help                      Yardımı göster
```

---

**Sonraki:** [Bakım Modu →](maintenance.md)
