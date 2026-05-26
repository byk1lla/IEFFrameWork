# Medya Kütüphanesi

- [Giriş](#giris)
- [Upload](#upload)
- [Galeri Görünümü](#galeri-gorunumu)
- [URL Kullanımı](#url-kullanimi)
- [Programatik Erişim](#programatik-erisim)
- [Storage Helper'ı](#storage-helperi)

---

## Giriş

Admin > Medya — `/admin/media` — yüklenen tüm görsel/video/dosyalar için merkezi galeri. Drag & drop ile yükle, kopyala-yapıştır URL'i blog yazısında veya editör'de kullan.

---

## Upload

| Method | URL | İşlev |
|---|---|---|
| GET | `/admin/media` | Galeri |
| POST | `/admin/media` | Dosya yükle |
| POST | `/admin/media/{id}/delete` | Sil (SweetAlert onayı) |

İzin verilen uzantılar: `jpg, jpeg, png, webp, gif, svg, mp4, webm, mov, pdf, doc, docx, xls, xlsx`.
Max boyut: 20MB (üst tabaka), `php.ini`'ye göre değişir.

---

## Galeri Görünümü

- Grid layout (responsive)
- Görsel ön-izleme
- Dosya adı + boyut + tarih + tip badge
- Tek tıkla URL kopyala
- Çoklu seçim + toplu silme
- Filtre: tip (görsel/video/doküman) + arama

---

## URL Kullanımı

Yüklenen dosya:

```
/uploads/2026/05/abc123_logo.png
```

Doğrudan src olarak kullan:

```html
<img src="/uploads/2026/05/abc123_logo.png" alt="">
```

---

## Programatik Erişim

`App\Models\Media`:

```php
use App\Models\Media;

$all   = Media::all();
$file  = Media::find(42);
$image = Media::create([
    'filename'      => 'abc.png',
    'original_name' => 'logo.png',
    'mime'          => 'image/png',
    'size'          => 12345,
    'url'           => '/uploads/...',
]);
```

---

## Storage Helper'ı

`App\Core\Storage` — dosya işlemleri için wrapper:

```php
use App\Core\Storage;

// Upload (CSRF gerekli)
$url = Storage::upload($request->file('avatar'), 'avatars');

// Sil
Storage::delete('/uploads/2026/05/abc.png');

// Var mı?
Storage::exists('/uploads/file.pdf');

// Tüm dosyaları listele
$files = Storage::files('/uploads/2026/05');
```

`Storage::upload()`:
- Güvenli dosya adı üretir (`<timestamp>_<random>_<safename>`)
- MIME type kontrolü
- Boyut kontrolü
- `public/uploads/YYYY/MM/` klasörüne yazar (otomatik dizin oluşturur)

---

**Sonraki:** [Loglar →](logs.md)
