# Blog & İçerik

- [Giriş](#giris)
- [Veri Modeli](#veri-modeli)
- [Frontend](#frontend)
- [Admin: Yazı Yönetimi](#admin-yaz-yonetimi)
- [AI ile Oluştur](#ai-ile-olustur)
- [Kategoriler](#kategoriler)
- [SEO Alanları](#seo-alanlari)
- [Quill Editör](#quill-editor)

---

## Giriş

IEF, hemen kullanıma hazır bir blog modülü içerir: yazı CRUD, kategori, slug, kapak görseli, SEO meta, Quill 2.0 zengin metin editör, isteğe bağlı **AI ile içerik üretimi** (Groq).

---

## Veri Modeli

### `posts` tablosu

```
id, title, slug, excerpt, body (longtext, HTML),
cover, category_id, user_id (yazar),
published, published_at,
meta_title, meta_description, og_image, is_featured,
created_at, updated_at
```

### `post_categories` tablosu

```
id, name, slug, color, created_at, updated_at
```

İlişkiler manuel — Post modelinde:

```php
public function category(): ?PostCategory {
    return PostCategory::find($this->category_id);
}

public function author(): ?User {
    return User::find($this->user_id);
}
```

---

## Frontend

| URL | İşlev |
|---|---|
| `/blog` | Yayınlanmış yazılar listesi (pagination, kategori filtresi) |
| `/blog/{slug}` | Tek yazı sayfası |

Views: `app/Views/blog/index.php`, `app/Views/blog/show.php`.

---

## Admin: Yazı Yönetimi

**Admin > Blog** — `/admin/blog`

| URL | İşlev |
|---|---|
| `/admin/blog` | Yazı listesi (DataTable: başlık, kategori, durum, tarih) |
| `/admin/blog/create` | Yeni yazı |
| `/admin/blog/{id}/edit` | Düzenle |
| `/admin/blog/{id}/update` | POST: kaydet |
| `/admin/blog/{id}/delete` | POST: sil (SweetAlert onayı) |
| `/admin/blog/ai/generate` | POST: AI ile içerik üret (Groq) |

---

## AI ile Oluştur

`config/services.php`'de Groq API key tanımlıysa, yeni yazı sayfasında **"AI ile Oluştur"** butonu aktif olur.

Akış:
1. Konu/başlık gir
2. Buton'a tıkla → POST `/admin/blog/ai/generate`
3. Backend Groq'a istek atar (varsayılan model `llama-3.3-70b-versatile`)
4. Üretilen başlık + excerpt + body Quill editöre otomatik doldurulur

> Detay: [AI / Groq →](ai-groq.md)

---

## Kategoriler

Yazılar bir kategoriye atanabilir (opsiyonel). Kategori CRUD basit bir sayfa olarak yapılır — frontend'de filtre olarak gözükür.

`post_categories` tablosu:
- `color` alanı kategori chip'inin rengini belirler (örn. `#3b82f6`)

---

## SEO Alanları

Her yazıda:

- **Meta Title** (max 70 karakter — Google snippet'ı için optimal)
- **Meta Description** (max 160 karakter)
- **OG Image** (sosyal paylaşımda gözükecek 1200x630 görsel)
- **Is Featured** (anasayfa ön plan listesinde gözüksün mü)

`/blog/{slug}` sayfasında bu meta'lar otomatik `<head>`'e enjekte edilir.

---

## Quill Editör

Blog admin'inde [Quill 2.0](https://quilljs.com) zengin metin editörü kullanılır:

- Heading, bold, italic, underline
- List (ordered/unordered)
- Link, image, video embed
- Code block, blockquote
- Renk, hizalama
- HTML olarak `posts.body`'ye kaydedilir

Frontend'de yazı içeriği:

```blade
<article class="prose lg:prose-lg max-w-none">
    {!! $post->body !!}
</article>
```

> `body` alanı HTML olduğu için **çıkışta sanitize etme** — admin yazısıdır, güvendiğin kaynak. Yorum gibi user-input HTML için DOMPurify veya HTMLPurifier kullan.

---

**Sonraki:** [Medya →](media.md)
