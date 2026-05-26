# AI · Groq Entegrasyonu

- [Giriş](#giris)
- [Neden Groq?](#neden-groq)
- [API Key Alma](#api-key-alma)
- [Konfigürasyon](#konfigurasyon)
- [Kullanım](#kullanim)
- [Model Seçimi](#model-secimi)
- [Güvenlik](#guvenlik)

---

## Giriş

IEF, [Groq Cloud](https://groq.com) üzerinden büyük dil modellerine (Llama 3.3 vb.) hızlı çıkarım sağlar. Şu an entegrasyon **blog içerik üretimi** için kullanılıyor — gelecekte daha fazla yere genişleyecek.

---

## Neden Groq?

- **En hızlı LLM çıkarımı** — saniyede 600+ token, OpenAI'den 10x hızlı
- **Ücretsiz tier** geliştirme için yeterli
- **OpenAI-uyumlu API** — `chat/completions` endpoint
- Llama 3.3 70B, Mixtral, Gemma 2 modelleri

---

## API Key Alma

1. [console.groq.com/keys](https://console.groq.com/keys) — ücretsiz hesap
2. Yeni key oluştur: `gsk_xxxxxxxxxxxxxxxxxxxxx`
3. Kopyala (sadece bir kez gösterilir)

---

## Konfigürasyon

> **Önemli:** API key DB'de **değil**, `config/services.php` dosyasında tutulur — DB yedeklerinde sızmasın, `.gitignore`'a alınabilsin diye.

`config/services.php`:

```php
return [
    'groq' => [
        'api_key' => 'gsk_xxxxxxxxxxxxxxxxxxxxx',
        'model'   => 'llama-3.3-70b-versatile',  // varsayılan
    ],
];
```

Key girildikten sonra **Admin > Ayarlar > AI** sekmesinde yeşil **"Groq aktif"** badge'i görünür. Aksi halde **"Groq API key tanımlı değil"** uyarısı.

> Repo'ya key sızdırmamak için: `echo "config/services.php" >> .gitignore`

---

## Kullanım

`App\Services\GroqService`:

```php
use App\Services\GroqService;

$ai = new GroqService();

$response = $ai->complete([
    ['role' => 'system', 'content' => 'Sen yardımcı bir SEO uzmanısın.'],
    ['role' => 'user',   'content' => 'PHP framework konulu bir blog yazısı taslağı oluştur.'],
]);

echo $response['content'];   // model çıktısı
```

Streaming (server-sent events):

```php
$ai->completeStream($messages, function (string $chunk) {
    echo $chunk;
    ob_flush(); flush();
});
```

JSON mode (yapılandırılmış çıktı):

```php
$response = $ai->complete($messages, [
    'response_format' => ['type' => 'json_object'],
]);

$json = json_decode($response['content'], true);
// ['title' => ..., 'excerpt' => ..., 'body' => ...]
```

---

## Model Seçimi

Groq'un sunduğu modeller (2026 itibariyle):

| Model | Hız | Kalite | Konteks | Kullanım |
|---|---|---|---|---|
| `llama-3.3-70b-versatile` | hızlı | yüksek | 128K | Genel kullanım, blog (varsayılan) |
| `llama-3.1-8b-instant` | çok hızlı | orta | 128K | Hızlı yanıt, basit görevler |
| `mixtral-8x7b-32768` | hızlı | yüksek | 32K | Çok dilli, çok-uzman |
| `gemma2-9b-it` | hızlı | iyi | 8K | Kısa görevler |

Değiştirmek için `config/services.php`'deki `groq.model`'i güncelle.

---

## Güvenlik

- API key **asla** frontend'e gönderme; tüm Groq çağrıları backend'den yapılır
- AI endpoint'leri admin auth + CSRF koruması altında olmalı
- Rate limit kullan (Groq tier'larında token quota var — patlatmamak için)
- User input'u doğrudan prompt'a koymadan önce sanitize et (prompt injection riski)

```php
$userQuery = strip_tags($this->request->input('query'));
$userQuery = mb_substr($userQuery, 0, 500);

$response = $ai->complete([
    ['role' => 'system', 'content' => 'Sen yardımcısın. Sadece konuyla ilgili yanıt ver.'],
    ['role' => 'user',   'content' => $userQuery],
]);
```

---

**Sonraki:** [AI ile Blog Üretimi →](blog-ai.md)
