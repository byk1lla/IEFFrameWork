# Randevu Sistemi

- [Giriş](#giris)
- [Frontend](#frontend)
- [Admin](#admin)
- [Servisler](#servisler)

---

## Giriş

`/randevu` — public randevu talep formu. Müşteri tarih/saat seçer, servis tipi belirler, submit eder. Admin onaylar/iptal eder.

İki tablo:
- `appointment_services` — servis tipleri (ad, süre dk, fiyat)
- `appointments` — talepler (ad, telefon, e-posta, tarih, durum)

---

## Frontend

| URL | İşlev |
|---|---|
| GET `/randevu` | Form |
| POST `/randevu` | Talep oluştur |
| GET `/randevu/tesekkurler` | Onay sayfası |

Form alanları: isim, telefon, e-posta, servis (dropdown), tarih, saat, not.

---

## Admin

**Admin > Randevular** — `/admin/appointments`

| URL | İşlev |
|---|---|
| `/admin/appointments` | DataTable: tarih, müşteri, servis, durum |
| `/admin/appointments/{id}` | Detay |
| `/admin/appointments/{id}/status` (POST) | Durum güncelle (pending→confirmed→cancelled) |
| `/admin/appointments/{id}/delete` (POST) | Sil |

Durum değişikliklerinde müşteriye otomatik mail gider (Mail driver smtp ise).

---

## Servisler

CRUD admin panelinde yapılır (`appointment_services` tablosu). Frontend dropdown'ında listelenir.

Alanlar: `name`, `slug`, `duration_minutes`, `price`, `description`, `active`.

---

**Sonraki:** [SEO →](seo.md)
