# Site Editör (Inline)

- [Giriş](#giris)
- [Editör Modunu Açma](#editor-modunu-acma)
- [Düzenleme vs Gezinme Modu](#duzenleme-vs-gezinme-modu)
- [İçerik Tipleri](#icerik-tipleri)
    - [Metin](#metin)
    - [Görsel](#gorsel)
    - [İkon](#ikon)
- [Tema Sidebar (Görünüm)](#tema-sidebar-gorunum)
- [Manuel İçerik Helper'ları](#manuel-icerik-helperlari)
- [Auto-Scanner](#auto-scanner)
- [Endpoint'ler](#endpointler)

---

## Giriş

IEF, **canlı sayfa üzerinde tıklayarak düzenleme** sunan bir inline editör barındırır. Admin login olmuş kullanıcı sitenin herhangi bir sayfasında editör modunu açabilir, metin/görsel/ikonlara tıklayıp düzenleyebilir. Değişiklikler otomatik kaydedilir, tüm ziyaretçiler için anında etkili olur.

Editör altyapısı:
- `app/Core/SiteContent.php` — içerik repository
- `app/Controllers/Admin/ContentEditorController.php` — save/upload endpoint'leri
- `public/assets/js/editor.js` — frontend logic
- `public/assets/css/editor.css` — editor stilleri
- `site_content` tablosu — content store (key-value)

---

## Editör Modunu Açma

Admin olarak giriş yaptıktan sonra üç yol:

1. **Nav'daki "Düzenle" butonu** — site sayfalarında otomatik görünür
2. **Direkt URL** — `https://example.com/site-editor`
3. **Admin > Editör menüsü** — `/admin/editor`

Editör açılınca:
- Üstte kırmızı-amber gradient bar görünür
- Session'a `site_editor.active = true` yazılır
- Sayfayı normal şekilde gezerken bile editör bar açık kalır
- Çıkmak için bar'daki **Kapat** → `/site-editor/cikis`

---

## Düzenleme vs Gezinme Modu

Bar'da bir toggle butonu vardır:

| Mod | Bar rengi | Davranış |
|---|---|---|
| **Düzenleme** | Kırmızı-amber | Editable alanlarda dashed border, tıklayınca yazı düzenle. Linkler **çalışmaz**. |
| **Gezinme** | Siyah-gri | Border yok, linkler **çalışır**, sayfada normal gezilebilir. |

Toggle butonuyla mod değiştirilir. Sayfa yenilenmeden de geçişler anlık.

---

## İçerik Tipleri

### Metin

`h1-h6`, `p`, `li`, `blockquote`, `figcaption`, `button` elementleri otomatik düzenlenebilir hale gelir (auto-scanner ile). Üzerine tıkla → metni yaz → başka yere tıkla → otomatik kaydedilir (~ 400ms debounce).

- `Enter`: blur (kaydet)
- `Escape`: değişikliği iptal et

### Görsel

`<img>` elementleri otomatik wrapper'la sarılır, üzerlerinde **"📷 Değiştir"** overlay görünür. Tıkla → modal açılır:

- Dosya seç (drag & drop destekli)
- URL yapıştır
- Max 20MB
- jpg/png/webp/gif/svg/mp4/webm/mov

Yüklenen dosyalar `public/assets/img/content/` klasörüne kaydedilir.

### İkon

Font Awesome ikonları (`<i class="fa-solid fa-...">`) tıklanabilir hale gelir. Modal'da:

- 80+ curated FA icon (araba, kullanıcı, takvim, vb. — tema-uygun)
- Arama
- Tıklayınca anında uygulanır + kaydedilir

---

## Tema Sidebar (Görünüm)

Bar'daki **"Tema"** butonu sağdan açılan bir sidebar sunar:

### Logo & Görseller

- Logo (açık tema)
- Logo (koyu tema)
- Favicon
- Varsayılan OG görsel

Her birinde drag-drop / dosya seç / URL yapıştır / kaldır seçeneği.

### Renk Paleti

- Primary color (color picker + hex input, sync)
- Accent color

### Tipografi

- Font ailesi seçimi (10 Google Font: Poppins, Inter, Roboto, ...)

### Footer Metinleri

- Tanıtım metni (textarea)
- Telif satırı — `{year}` ve `{site_name}` placeholder'ları desteklenir

Her değişiklik 400ms debounce sonrası otomatik kaydedilir; yeşil "kaydedildi ✓" işareti yanıp söner.

---

## Manuel İçerik Helper'ları

Auto-scanner'ın yakalamadığı veya custom yerleştirmek istediğin içerikler için view helper'ları:

```blade
{!! editable('home.hero.title', 'Hoş geldin!', ['tag' => 'h1', 'class' => 'text-5xl']) !!}
{!! editable('home.hero.lead', 'Lead text...', ['tag' => 'p', 'class' => 'text-lg']) !!}

{!! editable_image('home.hero.bg', '/assets/img/default.jpg', ['alt' => 'Hero', 'class' => 'w-full']) !!}

{!! editable_icon('home.feature.1.icon', 'fa-solid fa-bolt', ['class' => 'text-3xl text-blue-600']) !!}
```

Onur tarzı `content()` helper'ları da var (aynı `site_content` tablosuna yazar):

```blade
{!! content('home.about.body', '<p>Hakkımızda...</p>', ['type' => 'html', 'tag' => 'div']) !!}
{!! content_image('home.gallery.1') !!}
{!! content_icon('home.icon.support', 'fa-solid fa-headset') !!}
```

---

## Auto-Scanner

`editor.js`'in başlatma adımında `<main>` içindeki tüm metin/görsel elementleri xpath bazlı bir key ile otomatik **editable** yapılır:

```
key format: auto.{normalized-path}.{xpath}
örnek:      auto.home.h1[0]/span[0]
```

Bu sayede her sayfada **özel kod yazmadan** içerik düzenlenebilir. Auto-scan dışında bırakmak için:

```html
<div data-no-edit>Bu blok dışarıda kalır</div>
```

---

## Endpoint'ler

| Method | URL | İşlev |
|---|---|---|
| GET | `/site-editor` | Edit modunu aç (admin only) + `/`'a redirect |
| GET | `/site-editor/cikis` | Edit modunu kapat + referer'a dön |
| POST | `/admin/editor/save` | İçerik kaydet (key + value + type) |
| POST | `/admin/editor/upload` | Görsel/video yükle, URL döndür |
| POST | `/site-editor/appearance/save` | Tema/görünüm Setting'i kaydet |

---

**Sonraki:** [Ayarlar →](settings.md)
