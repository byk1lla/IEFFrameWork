# Logging

- [Giriş](#giris)
- [Log Yazma](#log-yazma)
- [Log Seviyeleri](#log-seviyeleri)
- [Context ile Loglama](#context-ile-loglama)
- [Log Dosyaları](#log-dosyalari)
- [Log Görüntüleyici (Admin)](#log-goruntuleyici-admin)
- [Log Rotasyonu](#log-rotasyonu)

---

## Giriş

IEF, uygulamanın olaylarını dosyaya, sistem hata logoluna veya Slack'e (özelleştirmeyle) loglayabilen güçlü logging servisleri sağlar. IEF, ardındaki [Monolog](https://github.com/Seldaek/monolog) kütüphanesini kullanır.

---

## Log Yazma

`App\Core\Logger` sınıfı üzerinden:

```php
use App\Core\Logger;

Logger::info('Kullanıcı kayıt oldu');
Logger::warning('Şüpheli login denemesi', ['ip' => $ip]);
Logger::error('Ödeme başarısız', ['order_id' => 42]);
Logger::critical('Veritabanı bağlantısı koptu');
```

---

## Log Seviyeleri

PSR-3 standardına uyumlu:

| Seviye | Kullanım |
|---|---|
| `debug` | Detaylı debug bilgisi (sadece dev) |
| `info` | İlginç olay (kullanıcı login oldu) |
| `notice` | Normal ama dikkate değer olay |
| `warning` | Olağandışı durum, henüz hata değil |
| `error` | Çalışma zamanı hatası, eylem gerekebilir |
| `critical` | Kritik bileşen kullanılamıyor |
| `alert` | Hemen müdahale gerekli |
| `emergency` | Sistem kullanılamıyor |

```php
Logger::log('info', 'mesaj', ['context' => 'value']);
```

---

## Context ile Loglama

İkinci argüman associative array — loga JSON olarak eklenir, sonradan filtrelemek için kullanışlı:

```php
Logger::error('Mail gönderilemedi', [
    'to'    => $email,
    'error' => $exception->getMessage(),
    'trace' => $exception->getTraceAsString(),
]);
```

Log dosyasında:

```
[2026-05-25 14:23:12] app.ERROR: Mail gönderilemedi {"to":"x@y.com","error":"SMTP timeout","trace":"..."}
```

---

## Log Dosyaları

Varsayılan olarak `storage/logs/` dizinine günlük dosyalar:

```
storage/logs/
├── app-2026-05-23.log
├── app-2026-05-24.log
├── app-2026-05-25.log
└── mail-2026-05-25.log    # mail driver "log" ise mail içerikleri buraya
```

> `storage/logs/` web sunucusu tarafından **yazılabilir** olmalı (`chmod 775`).

---

## Log Görüntüleyici (Admin)

Logları admin panelden görüntüle:

**Admin > Loglar**

Özellikler:
- Tarih seçici (gün bazında dosya)
- Level filtresi (INFO, WARNING, ERROR, ...)
- Mesaj arama
- JSON context expand

---

## Log Rotasyonu

Günlük dosyalar otomatik oluşur. Eski dosyaları temizlemek için:

```bash
# Cron — 30 günden eski log'ları sil
0 3 * * * find /var/www/projem/storage/logs -name "*.log" -mtime +30 -delete
```

Veya sistem `logrotate`:

```
# /etc/logrotate.d/ief-framework
/var/www/projem/storage/logs/*.log {
    daily
    rotate 30
    compress
    missingok
    notifempty
}
```

---

**Sonraki:** [Veritabanı: Başlangıç →](database.md)
