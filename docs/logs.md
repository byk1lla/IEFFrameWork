# Log Görüntüleyici

- [Giriş](#giris)
- [Erişim](#erisim)
- [Filtreleme](#filtreleme)
- [Log Dosyaları](#log-dosyalari)
- [Manuel Loglama](#manuel-loglama)

---

## Giriş

Admin > Loglar — `/admin/logs` — `storage/logs/` dizinindeki log dosyalarını tarayıcıdan görüntüleme arayüzü. Sunucuya SSH gerekmez.

---

## Erişim

Yalnızca admin (login + role) erişebilir. Path: `/admin/logs`.

---

## Filtreleme

Toolbar:

- **Tarih seçici** — gün bazında log dosyası (`app-2026-05-25.log`)
- **Level filtresi** — DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY
- **Arama** — message + context içinde substring arama

Satır:
- Timestamp
- Level (renkli badge)
- Channel (app, mail, ...)
- Message
- "Detay" → context JSON expand

---

## Log Dosyaları

Dosya formatı (Monolog default):

```
[2026-05-25 14:30:12] app.INFO: Kullanıcı kayıt oldu {"email":"x@y.com"} []
[2026-05-25 14:30:18] app.ERROR: Mail gönderilemedi {"to":"x@y.com","error":"SMTP timeout"} []
[2026-05-25 14:30:25] mail.INFO: Mail kuyruğa alındı {"subject":"Hoş Geldin"} []
```

Dosya yolu: `storage/logs/<channel>-<YYYY-MM-DD>.log`

| Channel | Ne loglanır |
|---|---|
| `app` | Genel uygulama olayları, exception'lar |
| `mail` | Mail driver "log" iken — mail içerikleri |

> Channel eklemek için `Logger::channel('payment')->info(...)` (Monolog handler eklenmesi gerekir).

---

## Manuel Loglama

```php
use App\Core\Logger;

Logger::info('Kullanıcı kayıt oldu', ['email' => $email]);
Logger::warning('Olağandışı', ['ip' => $ip]);
Logger::error('Ödeme başarısız', ['order_id' => 42, 'reason' => $e->getMessage()]);
```

Tüm context array'ler JSON olarak log satırına eklenir; arama ile filtrelenebilir.

> Detay: [Logging →](logging.md)

---

**Sonraki:** [Kullanıcılar →](users.md)
