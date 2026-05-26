# CSRF Koruması

- [Giriş](#giris)
- [CSRF Saldırısı Nedir?](#csrf-saldrs-nedir)
- [Token Yerleştirme](#token-yerletirme)
- [Token Doğrulama](#token-dorulama)
- [AJAX İstekleri](#ajax-istekleri)
- [İstisnalar](#istisnalar)
- [Token Yenileme](#token-yenileme)

---

## Giriş

Cross-Site Request Forgery (CSRF), kötü niyetli bir sitenin kimliği doğrulanmış bir kullanıcı adına başka bir siteye istek göndermesini sağlayan bir saldırı türüdür. IEF Framework, uygulamanı bu saldırılardan korumayı kolaylaştırır.

---

## CSRF Saldırısı Nedir?

Şu senaryoyu düşün: uygulaman, kimliği doğrulanmış kullanıcının e-posta adresini değiştirmek için `POST /email/change` rotası sunuyor. Büyük olasılıkla, bu rota yeni e-posta adresini içeren bir `email` input bekler.

CSRF koruması olmadan, kötü niyetli bir web sitesi şuna benzer bir HTML form içerebilir ve bunu kullanıcının tarayıcısı tarafından otomatik olarak gönderebilir:

```html
<form action="https://your-app.com/email/change" method="POST">
    <input type="hidden" name="email" value="malicious@attacker.com">
</form>
<script>document.forms[0].submit();</script>
```

Kötü niyetli web sitesi sayfayı yüklendiğinde otomatik olarak form'u submit ederse, kullanıcının e-posta adresi saldırgan tarafından kontrol edilen bir değere değişir.

---

## Token Yerleştirme

IEF, uygulaman tarafından yönetilen her aktif kullanıcı oturumu için otomatik olarak bir CSRF "token" oluşturur. Bu token, isteği gerçekten kimliği doğrulanmış kullanıcının yaptığını doğrulamak için kullanılır.

Form'a token'ı yerleştirmek için Blade-lite `@csrf` direktifini kullan:

```blade
<form method="POST" action="/iletisim">
    @csrf
    <input type="text" name="name">
    <input type="email" name="email">
    <button>Gönder</button>
</form>
```

Render edilince:

```html
<form method="POST" action="/iletisim">
    <input type="hidden" name="_csrf_token" value="a8f3...e2c1">
    <input type="text" name="name">
    ...
</form>
```

Helper de kullanılabilir:

```php
$token = csrf_token();
```

---

## Token Doğrulama

Tüm POST/PUT/PATCH/DELETE istekleri controller içinde doğrulanmalıdır:

```php
public function submit(): Response
{
    $this->abortIfInvalidCsrf();
    // ...
}
```

Token geçersizse `419 Page Expired` döner ve istek sonlanır.

Manuel kontrol için:

```php
use App\Core\Session;

if (!Session::verifyCsrfToken($this->request->input('_csrf_token'))) {
    abort(419);
}
```

---

## AJAX İstekleri

Fetch/Axios istekleri için token'ı meta tag üzerinden gönder:

```blade
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

```js
const token = document.querySelector('meta[name="csrf-token"]').content;

fetch('/api/messages', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': token,
    },
    body: JSON.stringify({ subject: 'Selam' })
});
```

Server tarafı header'ı veya body'deki `_csrf_token` field'ını otomatik kontrol eder.

> **Tip:** `layouts/app.php` zaten `<meta name="csrf-token">` içeriyor — `$csrf_token` değişkeni otomatik geçilir.

---

## İstisnalar

Bazı endpoint'leri CSRF kontrolünden hariç tutmak isteyebilirsin (örn. webhook'lar). En basit yol: `abortIfInvalidCsrf()`'yi **çağırma**:

```php
public function webhook(): Response
{
    // CSRF kontrolü yok — webhook signature başka bir mekanizmayla doğrulanır
    $signature = $this->request->header('x-webhook-signature');
    if (!$this->verifyWebhookSignature($signature)) {
        abort(401);
    }
    // ...
}
```

> Webhook'ları **mutlaka** alternatif bir mekanizmayla (HMAC signature, secret token) doğrula.

---

## Token Yenileme

Session ID değiştiğinde (login, logout, regenerate) yeni token üretilir. Manuel yenileme:

```php
\App\Core\Session::regenerateCsrfToken();
```

Token süreleri varsayılan olarak session ömrü kadardır. Bunu kısaltmak için: Admin > Ayarlar > Güvenlik > CSRF Token Ömrü.

---

**Sonraki:** [Sessions →](sessions.md)
