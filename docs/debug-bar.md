# Debug Bar (Titan Pulse)

- [Giriş](#giris)
- [Açma / Kapatma](#acma-kapatma)
- [Görüntülenen Bilgiler](#goruntulenen-bilgiler)
- [SQL Query Profiling](#sql-query-profiling)

---

## Giriş

**Titan Pulse** — IEF'in built-in debug bar'ı. Her sayfanın altında sticky bir çubuk; o request'in performans verilerini gösterir. Sadece geliştirme için, production'da otomatik gizli.

---

## Açma / Kapatma

İki kontrol:

1. `config/app.php` → `debug: true` (master switch — false ise hiç görünmez)
2. **Admin > Ayarlar > Genel** → ☑ "Debug bar" (debug açıkken bile alt çubuğu gizlemek için)

```php
// config/app.php
'debug' => true,   // true: dev | false: production
```

---

## Görüntülenen Bilgiler

Alt çubukta:

```
ief-framework  debug bar    route GET /admin   sql 12   time 84.5ms   mem 4.21MB   php 8.3.1
```

| Alan | Açıklama |
|---|---|
| `route` | HTTP method + URI |
| `sql` | Bu request'te çalışan SQL sorgu sayısı |
| `time` | Toplam süre (ms) |
| `mem` | Peak memory kullanımı |
| `php` | PHP versiyonu |

Tıklayınca açılan paneller:

- **Queries** — tüm SQL'ler, binding'leri, süreleri
- **Route** — eşleşen route + controller@method + middleware'ler
- **Session** — session içeriği
- **Config** — yüklü `config/*` değerleri
- **Logs** — bu request'te yazılan log mesajları

---

## SQL Query Profiling

Her query otomatik kayıt edilir:

```
1. SELECT * FROM users WHERE id = ?       [42]               2.1ms
2. SELECT * FROM posts WHERE user_id = ?  [42]               4.8ms
3. SELECT COUNT(*) FROM messages WHERE ...                  12.3ms ⚠
```

> ⚠ 10ms'den uzun query'ler kırmızı işaretlenir.

N+1 problemini hızlıca tespit etmek için **sayı**ya bak — `sql 50+` görüyorsan muhtemelen döngüde query atıyorsundur.

---

## Production'da

`config/app.php` → `debug: false` → bar çıkmaz, query log'lama da devre dışı. Performance overhead sıfır.

> Debug bar'ı production'da **asla** açma — kullanıcıya SQL, session, config sızar.

---

**Sonraki:** [Performans →](performance.md)
