# İletişim Formu

- [Giriş](#giris)
- [Frontend](#frontend)
- [Backend Akış](#backend-akis)
- [Spam Koruması](#spam-korumasi)
- [Admin Yönetimi](#admin-yonetimi)

---

## Giriş

`/iletisim` — hazır iletişim formu sayfası. Form submit'leri `messages` tablosuna kaydedilir + admin'e mail bildirimi gider.

---

## Frontend

| URL | İşlev |
|---|---|
| GET `/iletisim` | Form sayfası |
| POST `/iletisim` | Submit |
| GET `/iletisim/tesekkurler` | Başarı sayfası |

Form template: `app/Views/contact/index.php` — Tailwind tasarımı, CSRF + honeypot ile korumalı.

---

## Backend Akış

`App\Controllers\ContactController@submit`:

1. CSRF doğrula
2. Honeypot field (gizli `website` input) boş mu kontrol et
3. Rate limit (IP başına saatte 5)
4. Validation: name (required, max 100), email (required, email), message (required, min 10, max 2000)
5. `Message::create([...])` — DB kaydı
6. Admin'e mail (Settings > Mail > Admin Inbox tanımlıysa)
7. Flash success + redirect `/iletisim/tesekkurler`

---

## Spam Koruması

3 katmanlı:

1. **CSRF token** — sentinel forms-from-anywhere saldırılarına karşı
2. **Honeypot** — bot doldurursa istek sessizce reddedilir
3. **Rate limit** — aynı IP saatte 5 form (Settings > Güvenlik > Rate Limit)

> Daha güçlü koruma için reCAPTCHA v3 / hCaptcha entegre edilebilir — şu an built-in değil.

---

## Admin Yönetimi

**Admin > Mesajlar** — `/admin/messages`

- DataTable: isim, e-posta, konu kısa, tarih, durum (okundu/okunmadı)
- Detay sayfası: tam mesaj, yanıt için "mailto:" linki
- Toplu silme

---

**Sonraki:** [Randevu →](appointments.md)
