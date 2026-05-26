# Request

- [Giriş](#giris)
- [Request Etkileşimi](#request-etkileimi)
    - [Request Path & Method](#request-path-method)
    - [Request URL](#request-url)
    - [Header'lar](#headerlar)
    - [IP Adresi](#ip-adresi)
- [Input](#input)
    - [Tüm Input'lar](#tum-inputlar)
    - [Tek Değer Alma](#tek-deer-alma)
    - [JSON Input](#json-input)
    - [Query String](#query-string)
- [Cookie'ler](#cookieler)
- [Dosya Yüklemeleri](#dosya-yuklemeleri)
- [Method Spoofing](#method-spoofing)

---

## Giriş

`App\Core\Request` sınıfı mevcut HTTP isteğiyle nesne-yönelimli etkileşim sağlar. Form input'ları, query string parametreleri, header'lar, dosyalar — hepsine tek bir API üzerinden erişirsin.

Bir controller'dan request'e erişmek için base controller üzerindeki `$this->request` özelliğini kullan:

```php
public function store(): Response
{
    $name = $this->request->input('name');
    // ...
}
```

Veya `request()` helper'ı:

```php
$name = request('name');
$all  = request()->all();
```

---

## Request Etkileşimi

### Request Path & Method

```php
$path = $this->request->path();        // "/blog/abc"
$verb = $this->request->method();      // "GET" | "POST" | ...

if ($this->request->isMethod('post')) {
    // ...
}
```

### Request URL

```php
$url    = $this->request->url();        // https://example.com/blog?p=1
$base   = $this->request->root();       // https://example.com
$full   = $this->request->fullUrl();    // https://example.com/blog?p=1
$secure = $this->request->isSecure();   // bool
```

### Header'lar

```php
$ua    = $this->request->header('user-agent');
$auth  = $this->request->header('authorization');
$xhr   = $this->request->isAjax();      // X-Requested-With: XMLHttpRequest
```

### IP Adresi

```php
$ip = $this->request->ip();
```

> Reverse proxy arkasındaysan (nginx, Cloudflare), `X-Forwarded-For` header'ına dikkat et — `Request::ip()` otomatik olarak güvenli olduğu durumda bunu yorumlar.

---

## Input

### Tüm Input'lar

GET + POST birleştirilmiş halini diziye çevirir:

```php
$input = $this->request->all();
// ['name' => 'Efe', 'email' => 'efe@x.com', 'page' => '2']
```

### Tek Değer Alma

```php
$name  = $this->request->input('name');                // null|string
$page  = $this->request->input('page', 1);             // default 1
$bool  = $this->request->boolean('remember');          // checkbox için
```

İç içe input (`profile[name]`):

```php
$name = $this->request->input('profile.name');
```

Var mı kontrolü:

```php
if ($this->request->has('email')) {
    // ...
}
```

### JSON Input

`Content-Type: application/json` ile gelen istekte body otomatik parse edilir:

```js
fetch('/api/users', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name: 'Efe' }),
});
```

```php
$name = $this->request->input('name');   // 'Efe' — JSON'dan otomatik
```

### Query String

Sadece query string'den (GET parametreleri):

```php
$page = $this->request->query('page', 1);
```

---

## Cookie'ler

```php
$val = $this->request->cookie('name');

// Set: response üzerinden değil, doğrudan setcookie() ile (helper yok)
setcookie('name', 'value', time() + 3600, '/', '', true, true);
```

---

## Dosya Yüklemeleri

```php
if ($file = $this->request->file('avatar')) {
    $name = $file['name'];           // orijinal isim
    $tmp  = $file['tmp_name'];
    $size = $file['size'];
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if ($size > 5 * 1024 * 1024) {
        return $this->redirect('/profil')->withError('Max 5MB');
    }

    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    $dest = PUBLIC_PATH . '/uploads/' . time() . '_' . $safe;
    move_uploaded_file($tmp, $dest);
}
```

> Inline editör + medya kütüphanesi için `App\Core\Storage` helper'ı vardır — `Storage::upload($file, 'avatars')` gibi.

---

## Method Spoofing

HTML formlar yalnızca `GET` ve `POST` destekler. `PUT`, `PATCH`, `DELETE` route'lara form ile istek göndermek için `_method` hidden input'u kullan:

```html
<form method="POST" action="/posts/42/update">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <!-- ... -->
</form>
```

`Request::method()` artık `"PUT"` döner.

> `@csrf` Blade direktifi otomatik olarak `<input type="hidden" name="_csrf_token" value="...">` yerleştirir.

---

**Sonraki:** [Response →](responses.md)
