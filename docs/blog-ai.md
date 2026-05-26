# AI ile Blog Üretimi

- [Giriş](#giris)
- [Kurulum](#kurulum)
- [Kullanım](#kullanim)
- [Prompt Şablonu](#prompt-sablonu)
- [Endpoint](#endpoint)

---

## Giriş

Blog yazısı yazmak zaman alır. **Admin > Blog > Yeni Yazı** sayfasında **"AI ile Oluştur"** butonu, Groq Cloud üzerinden Llama 3.3 70B ile başlık + excerpt + body taslağı üretir. Sen sadece konuyu yazarsın.

---

## Kurulum

1. [Groq API key al](https://console.groq.com/keys) — ücretsiz
2. `config/services.php`'de tanımla:

```php
return [
    'groq' => [
        'api_key' => 'gsk_xxxxxxxxxxxxxxxxxxxxx',
        'model'   => 'llama-3.3-70b-versatile',
    ],
];
```

3. Admin > Ayarlar > AI'da yeşil **"Groq aktif"** badge'i görmelisin
4. Yeni yazı sayfasında **"AI ile Oluştur"** butonu artık aktif

> Detay: [AI / Groq →](ai-groq.md)

---

## Kullanım

1. **Admin > Blog > Yeni Yazı**
2. **Konu / Başlık ipucu** alanına yazılı taslağı gir, örn:
   ```
   PHP 8.3'ün yeni özellikleri ve nasıl kullanılır
   ```
3. **AI ile Oluştur** butonuna tıkla
4. ~5-10 saniye sonra:
   - **Başlık** alanı doldurulur
   - **Excerpt** alanı doldurulur (160 karakter)
   - **Body** alanına Quill editör'e zengin HTML eklenir (heading, list, code block dahil)
5. İçeriği gözden geçir, düzenle, yayınla

---

## Prompt Şablonu

`App\Services\GroqService` içindeki blog generator prompt:

```
Sen Türkçe bir blog yazarısın. Kullanıcı bir konu vereceğine, sen:
- Çekici bir başlık üret (max 70 karakter)
- 2-3 cümlelik excerpt (max 160 karakter)
- Detaylı, SEO-uyumlu blog yazısı (800-1200 kelime)
  - H2 ve H3 başlıklar
  - Numaralı ve maddeli listeler
  - Önemli kelimeleri **bold** yap
  - Kod gerekirse <pre><code>...</code></pre>

JSON formatında yanıt ver:
{
  "title": "...",
  "excerpt": "...",
  "body": "<h2>...</h2><p>..."
}
```

Bu prompt değiştirilebilir — `app/Services/GroqService.php` içinde `generateBlogPost()` method'unda.

---

## Endpoint

POST `/admin/blog/ai/generate`

Request:

```json
{
    "topic": "PHP 8.3'ün yeni özellikleri"
}
```

Response:

```json
{
    "ok": true,
    "title": "PHP 8.3: 7 Yenilik ve Pratik Kullanım Örnekleri",
    "excerpt": "PHP 8.3 ile gelen typed class constants, readonly amendments...",
    "body": "<h2>1. Typed Class Constants</h2><p>...</p>..."
}
```

CSRF token gerekli + admin auth.

Hata durumu:

```json
{
    "ok": false,
    "error": "Groq API key tanımlı değil — config/services.php'yi doldur."
}
```

---

## Maliyet

Groq free tier (2026 itibariyle):
- **30 istek/dakika** (RPM)
- **6000 istek/gün** (RPD)
- **30,000 token/dakika** (TPM)

Bir blog yazısı ~2000-3000 token tüketir → günde ~2000 yazı üretebilirsin (free tier).

Üst tier gerekirse: [console.groq.com/billing](https://console.groq.com/billing).

---

**Sonraki:** [Site Editör →](site-editor.md)
