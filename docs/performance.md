# Performans

- [Giriş](#giris)
- [OpCache](#opcache)
- [Composer Autoload](#composer-autoload)
- [Asset Optimization](#asset-optimization)
- [Database](#database)
- [Profiling](#profiling)

---

## Giriş

IEF küçük + hafiftir, ama trafik arttıkça optimize edilmesi gereken noktalar vardır.

---

## OpCache

PHP 8'in OpCache'i **mutlaka açık** olmalı:

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0   ; production'da
opcache.revalidate_freq=0
```

Production deploy sonrası: `php artisan cache:clear` benzeri bir komut yok — `service php-fpm reload` veya `opcache_reset()` çağır.

---

## Composer Autoload

Production deploy:

```bash
composer install --optimize-autoloader --no-dev --classmap-authoritative
```

- `--optimize-autoloader` — autoload sınıflarını classmap'e dump eder
- `--no-dev` — dev bağımlılıkları atla
- `--classmap-authoritative` — runtime PSR-4 lookup yapmaz

---

## Asset Optimization

- Tailwind: production build (`tailwindcss --minify`) — ~400KB → ~30KB
- Görseller: WebP'ye dönüştür (`cwebp -q 80 in.png -o out.webp`)
- Lazy loading: `<img loading="lazy">`
- CDN: nginx için `expires 30d;` + `Cache-Control: public`

---

## Database

- **Index'leri kullan** — `EXPLAIN` ile yavaş query'leri tespit et
- **N+1 sorunundan kaçın** — Debug Bar'da `sql 50+` görüyorsan döngüde query atıyorsun, manuel JOIN veya `whereIn` ile düzelt
- **Connection pooling** — PHP'de native değil; `pdo_mysql` persistent connection: `PDO::ATTR_PERSISTENT => true`
- **Slow query log** — MySQL'de aç:

```ini
slow_query_log = 1
long_query_time = 1
slow_query_log_file = /var/log/mysql/slow.log
```

---

## Profiling

### Debug Bar

Dev'de sayfa altında SQL/route/memory/süre. Hızlı tanı için yeterli.

> Detay: [Debug Bar →](debug-bar.md)

### Xdebug

Tam stack trace + profiler:

```ini
xdebug.mode=develop,debug,profile
xdebug.start_with_request=yes
xdebug.output_dir=/tmp/xdebug
```

`.cachegrind` dosyalarını KCacheGrind/Webgrind ile aç.

### Blackfire / Tideways

Production-safe APM araçları. IEF özel entegrasyon gerektirmez.

---

**Sonraki:** [Cache →](cache.md)
