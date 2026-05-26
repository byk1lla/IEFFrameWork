# Middleware

- [Giriş](#giris)
- [Middleware Tanımlama](#middleware-tanmlama)
- [Middleware Kaydetme](#middleware-kaydetme)
    - [Route'a Atama](#routea-atama)
    - [Route Grubuna Atama](#route-grubuna-atama)
- [Built-in Middleware'ler](#built-in-middlewareler)
- [Middleware Parametreleri](#middleware-parametreleri)
- [Sıralama](#sralama)

---

## Giriş

Middleware, uygulamaya gelen HTTP isteklerini incelemek ve filtrelemek için uygun bir mekanizma sağlar. Örneğin, IEF Framework, kullanıcının kimliğinin doğrulanıp doğrulanmadığını doğrulayan bir middleware içerir (`AuthMiddleware`). Kullanıcı kimliği doğrulanmamışsa, middleware kullanıcıyı login ekranına yönlendirir; aksi takdirde, middleware isteğin uygulamaya devam etmesine izin verir.

---

## Middleware Tanımlama

Yeni middleware oluştur:

```bash
./ief make:middleware EnsureAdmin
```

Bu, `app/Middleware/EnsureAdminMiddleware.php` dosyasını oluşturur:

```php
<?php

namespace App\Middleware;

use App\Core\Request;

class EnsureAdminMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        $user = \App\Core\Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Yetkin yok.');
            return false;
        }
        return true;
    }
}
```

İmza basittir:
- `handle()` `true` dönerse zincir devam eder, controller invoke edilir
- `false` dönerse veya `redirect()`/`abort()` çağrılırsa istek sonlanır

---

## Middleware Kaydetme

### Route'a Atama

> Şu an IEF route bazında tek tek middleware atama desteği sınırlıdır. Tipik kullanım grup üzerinden olur.

### Route Grubuna Atama

```php
Router::group([
    'prefix'     => '/admin',
    'middleware' => \App\Middleware\AuthMiddleware::class,
], function () {
    Router::get('/',         'AdminController@index');
    Router::get('/messages', 'Admin\MessageController@index');
});
```

Tüm `/admin/*` rotalarına `AuthMiddleware::handle()` çalışır.

Birden fazla middleware için dizi:

```php
Router::group([
    'prefix'     => '/admin',
    'middleware' => [
        \App\Middleware\AuthMiddleware::class,
        \App\Middleware\EnsureAdminMiddleware::class,
    ],
], function () {
    // ...
});
```

---

## Built-in Middleware'ler

| Sınıf | Görev |
|---|---|
| `App\Middleware\AuthMiddleware` | Login değilse `/login`'e redirect |

İlerleyen sürümlerde eklenecekler: `EnsureRole`, `RateLimit`, `CheckIp`, `ForceHttps`.

---

## Middleware Parametreleri

Path parametreleri `$params` olarak ikinci argümanda gelir:

```php
public function handle(Request $request, array $params = []): bool
{
    $id = $params['id'] ?? null;
    if ($id && Post::find($id)?->user_id !== Auth::id()) {
        abort(403);
        return false;
    }
    return true;
}
```

---

## Sıralama

Bir grupta birden fazla middleware varsa, dizide tanımladığın **sıra** ile çalışır. İlki `false` dönerse sonrakiler çalışmaz.

```php
'middleware' => [
    AuthMiddleware::class,        // 1. önce login kontrolü
    EnsureAdminMiddleware::class, // 2. sonra rol kontrolü
],
```

---

**Sonraki:** [CSRF Koruması →](csrf.md)
