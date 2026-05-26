# Bakım Modu

- [Giriş](#giris)
- [Bakım Modunu Açma](#bakim-modunu-acma)
- [Bakım Sayfası](#bakim-sayfasi)
- [Admin Bypass](#admin-bypass)
- [Erişilebilir Path'ler](#erisilebilir-pathler)
- [Özelleştirme](#ozellestirme)

---

## Giriş

Bir bakım/güncelleme yaparken sitenin tamamen kapanması veya yarım yamalak gözükmesi yerine, kullanıcılara modern bir **bakım sayfası** gösterilebilir. Bakım modu açıkken:

- Tüm public sayfa istekleri **HTTP 503** + bakım sayfası alır
- Admin paneli ve auth route'ları çalışır (admin işlem yapabilir)
- Admin login olmuş kullanıcı siteyi normal görür (kontrol için)
- Docs sayfaları (`/docs/*`) erişilebilir kalır

---

## Bakım Modunu Açma

**Admin > Ayarlar > Genel** sekmesinden:

```
☑ Bakım modu (yalnızca admin erişebilir)
```

Veya programatik olarak:

```php
Setting::set('general.maintenance', '1', 'bool');
```

Kapat:

```php
Setting::set('general.maintenance', '0', 'bool');
```

İsteğe bağlı özelleştirmeler:

```php
Setting::set('general.maintenance_message', 'Yeni özellikler ekliyoruz...');
Setting::set('general.maintenance_eta', '25 Mayıs 23:00');
```

---

## Bakım Sayfası

Sayfa: `app/Views/errors/maintenance.php`

Default tasarım:
- Beyaz tema, brand mavi/cyan renk paletinde
- Animasyonlu arka plan blob'ları + grid pattern
- 96x96 gradient ikon kutusu (içinde dönen çark)
- "HTTP 503 · Geçici Kesinti" badge
- "Sistem **Bakımda**" başlık (gradient text)
- Site adı + (varsa) özel mesaj
- (Varsa) "Tahmini açılış: ..." ETA kartı
- **Yeniden Dene** + **Giriş Yap** butonları
- (Varsa) E-posta / Telefon / WhatsApp iletişim kartı
- Footer + "HTTP/1.1 503 Service Unavailable" status

Sayfa otomatik 60 saniyede bir yenilenir (`setTimeout(reload, 60000)`).

---

## Admin Bypass

`App\Core\App::isUnderMaintenance()`:

```php
// Admin login olmuşsa bypass — siteyi normal görür
if (SiteContent::isAdminLoggedIn()) return false;
```

Yani:
1. Bakım modunu aç
2. Tarayıcıda admin olarak login ol (`/login`)
3. Siteyi normal şekilde gez — sayfalar açılır
4. Anonim ziyaretçi her sayfada bakım sayfası görür

Test için: incognito sekme ile siteyi aç → bakım sayfasını görürsün.

---

## Erişilebilir Path'ler

Bakım modunda her zaman erişilebilir route'lar:

- `/admin/*` — Admin paneli (login gerekli)
- `/login`, `/logout` — auth
- `/sifre-sifirla*` — şifre sıfırlama
- `/docs/*` — dokümantasyon
- `/site-editor*` — editör

Diğer her şey → 503 bakım sayfası.

Bypass listesini değiştirmek için `app/Core/App::isUnderMaintenance()` method'unu düzenle.

---

## Özelleştirme

### Tasarım

`app/Views/errors/maintenance.php` — tek dosya, embedded CSS. Renk, font, layout her şey burada düzenlenir.

### İçerik

- **Mesaj** — `general.maintenance_message` setting'i
- **ETA** — `general.maintenance_eta` setting'i (string, format serbest: "25 Mayıs 23:00", "2 saat sonra", vb.)
- **İletişim kartı** — `general.contact_email`, `general.contact_phone`, `general.whatsapp_number` setting'lerinden otomatik

### Status Code

503 + `Retry-After: 3600` header'ı default gönderilir. Bu Google'ın siteyi index'ten **düşürmemesine** yardım eder — geçici olduğunu bildirir.

---

**Sonraki:** [Debug Bar →](debug-bar.md)
