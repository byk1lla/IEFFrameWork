# Response

- [Response Oluşturma](#response-oluturma)
    - [String & Diziler](#string-diziler)
    - [Response Nesneleri](#response-nesneleri)
    - [Header'lar](#headerlar)
- [Redirect](#redirect)
    - [İsimlenmiş URL'lere Redirect](#isimlendirilmis-urllere-redirect)
    - [Flash Data ile Redirect](#flash-data-ile-redirect)
- [View Response'ları](#view-responseları)
- [JSON Response'ları](#json-responseları)
- [Dosya İndirme](#dosya-indirme)
- [Status Code'ları](#status-codelar)

---

## Response Oluşturma

Tüm route'lar ve controller'lar bir response döndürmelidir. IEF, çeşitli response döndürme yolları sunar.

### String & Diziler

En basit response — bir route'tan string döndür:

```php
Router::get('/', function () {
    return 'Hello World';
});
```

Bir dizi döndürürsen otomatik JSON'a çevrilir:

```php
Router::get('/saglik', function () {
    return ['ok' => true, 'time' => time()];
});
// Content-Type: application/json
// {"ok":true,"time":1716659999}
```

### Response Nesneleri

Tipik olarak, basit string veya dizi döndürmek yerine `App\Core\Response` instance'ları döneceksin. Bu, status code ve header'lar üzerinde tam kontrol sağlar:

```php
use App\Core\Response;

Router::get('/home', function () {
    return (new Response('Hello World', 200))
        ->header('X-Custom', 'value');
});
```

Controller'da base sınıf yardımcılarıyla daha temiz:

```php
public function show(): Response
{
    return $this->view('home');
}
```

### Header'lar

Header method'unu zincirleyebilirsin:

```php
return (new Response($content))
    ->header('Content-Type', 'text/plain')
    ->header('X-Header-1', 'A')
    ->header('X-Header-2', 'B');
```

---

## Redirect

Redirect response'ları, kullanıcıyı başka bir URL'ye yönlendirmek için gereken HTTP header'larını içerir.

```php
Router::get('/dashboard', function () {
    return redirect('/home/dashboard');
});
```

Controller'dan:

```php
return $this->redirect('/dashboard');
return $this->redirect('/dashboard', 301);  // kalıcı redirect
```

### Önceki URL'ye Geri Dön

```php
return back();
return back('/fallback');   // referer yoksa fallback
```

### Flash Data ile Redirect

Redirect ile birlikte session flash mesajı:

```php
$this->flash('success', 'Mesaj iletildi.');
return $this->redirect('/iletisim/tesekkurler');
```

View'da:

```blade
@if(flash('success'))
    <div class="alert alert-success">{{ flash('success') }}</div>
@endif
```

`flash()` helper'ı mesajı okur ve session'dan otomatik siler.

---

## View Response'ları

Bir view + data döndürme:

```php
public function show(int $id): Response
{
    $user = User::find($id);
    return $this->view('user.profile', [
        'user' => $user,
    ]);
}
```

Bu, `app/Views/user/profile.php` template'ini render eder ve `$user` değişkenini view'a iletir.

Helper kullanarak:

```php
return view('welcome', ['name' => 'Efe']);
```

> Detay: [Views (Blade-lite) →](views.md)

---

## JSON Response'ları

JSON döndürmek için iki yol:

```php
// 1) Dizi döndür — otomatik JSON
return ['ok' => true];

// 2) Controller helper'ı — status code ile
return $this->json(['ok' => true], 201);
```

İkisi de aynı header'ı set eder: `Content-Type: application/json; charset=utf-8`.

---

## Dosya İndirme

Bir dosyayı kullanıcının tarayıcısı tarafından indirilmeye zorlayacak şekilde gönder:

```php
public function download(int $id): Response
{
    $media = Media::find($id);
    $path  = PUBLIC_PATH . '/uploads/' . $media->filename;

    return (new Response(file_get_contents($path)))
        ->header('Content-Type', $media->mime)
        ->header('Content-Disposition', 'attachment; filename="' . $media->original_name . '"');
}
```

> Büyük dosyalar için `readfile()` + `exit()` daha bellek dostudur — `Response` body bellek tüketir.

---

## Status Code'ları

Yaygın HTTP status code'ları:

| Code | Anlam | Helper |
|---|---|---|
| 200 | OK | default |
| 201 | Created | `$this->json($data, 201)` |
| 204 | No Content | `new Response('', 204)` |
| 301 | Moved Permanently | `$this->redirect($url, 301)` |
| 302 | Found (geçici redirect) | `$this->redirect($url)` |
| 400 | Bad Request | `(new Response('', 400))` |
| 401 | Unauthorized | (auth middleware kullan) |
| 403 | Forbidden | `abort(403)` |
| 404 | Not Found | `abort(404)` |
| 419 | Page Expired (CSRF) | `$this->abortIfInvalidCsrf()` |
| 500 | Internal Server Error | ExceptionHandler |
| 503 | Service Unavailable | bakım modu |

`abort()` helper'ı `ExceptionHandler` üzerinden uygun error sayfasını render eder:

```php
if (!$post) {
    abort(404, 'Bu yazı bulunamadı.');
}
```

---

**Sonraki:** [Views (Blade-lite) →](views.md)
