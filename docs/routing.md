# Routing

- [Temel Route'lar](#temel-routelar)
- [Path Parametreleri](#path-parametreleri)
- [HTTP Method'ları](#http-methodlar)
- [Closure ile Route](#closure-ile-route)
- [Route Grupları](#route-gruplar)
- [Middleware](#middleware)
- [Route Listesi](#route-listesi)

---

## Temel Route'lar

Tüm route'lar `config/routes.php` içinde tanımlanır:

```php
use App\Core\Router;

Router::get('/',           'WelcomeController@index');
Router::get('/iletisim',   'ContactController@index');
Router::post('/iletisim',  'ContactController@submit');
Router::get('/blog/{slug}','BlogController@show');
```

İmza:

```php
Router::get(string $uri, string|callable $action)
Router::post(string $uri, string|callable $action)
```

`$action` ya `'Controller@method'` formatında string ya da bir closure'dur.

---

## Path Parametreleri

`{param}` syntax'ı ile yakala:

```php
Router::get('/blog/{slug}',           'BlogController@show');
Router::get('/users/{id}/edit',       'UserController@edit');
Router::get('/lang/{locale}', function (string $locale) {
    \App\Core\Lang::setLocale($locale);
    back();
});
```

Controller method'unda parametreler **sırayla** argüman olarak gelir:

```php
class BlogController extends Controller
{
    public function show(string $slug): Response
    {
        $post = Post::where('slug', $slug)->first();
        return $this->view('blog.show', ['post' => $post]);
    }
}
```

---

## HTTP Method'ları

Desteklenenler:

```php
Router::get($uri, $action);
Router::post($uri, $action);
Router::put($uri, $action);
Router::patch($uri, $action);
Router::delete($uri, $action);
```

> HTML formlardan `PUT`/`PATCH`/`DELETE` göndermek için `<input type="hidden" name="_method" value="PUT">` ekle — `Request` bunu yorumlar.

---

## Closure ile Route

Basit endpoint'ler için controller'a gerek yok:

```php
Router::get('/saglik', function () {
    return ['ok' => true, 'time' => time()];  // → JSON
});

Router::get('/site-editor', function () {
    if (!\App\Core\SiteContent::isAdminLoggedIn()) { redirect('/login'); return; }
    \App\Core\SiteContent::startEditing();
    redirect('/');
});
```

---

## Route Grupları

Ortak prefix + middleware için:

```php
Router::group([
    'prefix'     => '/admin',
    'middleware' => \App\Middleware\AuthMiddleware::class,
], function () {
    Router::get ('/',                  'AdminController@index');
    Router::get ('/messages',          'Admin\MessageController@index');
    Router::get ('/messages/{id}',     'Admin\MessageController@show');
    Router::post('/messages/{id}/delete', 'Admin\MessageController@destroy');
});
```

Bu şu route'ları üretir:

```
GET  /admin
GET  /admin/messages
GET  /admin/messages/{id}
POST /admin/messages/{id}/delete
```

Hepsine `AuthMiddleware::handle()` çalışır — login değilse `/login`'e redirect olur.

> İç içe grup desteklenir.

---

## Middleware

Middleware, controller'dan önce çalışan filtre. `app/Middleware/` altında:

```php
namespace App\Middleware;

use App\Core\Request;

class AuthMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        if (!\App\Core\Auth::check()) {
            redirect('/login');
            return false;
        }
        return true;
    }
}
```

- `true` döndürürse zincir devam eder, controller invoke edilir
- `false` veya `exit()`/`redirect()` ise istek sonlanır

Middleware'i route grubuna ekleme:

```php
Router::group(['middleware' => AuthMiddleware::class], function () {
    Router::get('/profil', 'UserController@profile');
});
```

> Detay: [Middleware →](middleware.md)

---

## Route Listesi

CLI'den tüm route'ları tablo halinde gör:

```bash
./ief route:list
```

```
METHOD URI                            ACTION
──────────────────────────────────────────────────────
GET    /                              WelcomeController@index
GET    /iletisim                      ContactController@index
POST   /iletisim                      ContactController@submit
GET    /blog                          BlogController@index
GET    /blog/{slug}                   BlogController@show
POST   /login                         AuthController@login
GET    /admin                         AdminController@index
GET    /admin/messages                Admin\MessageController@index
...
```

---

## URL Üretimi

Helper'lar:

```php
url('/blog/' . $post->slug);   // https://example.com/blog/abc
asset('img/logo.png');         // https://example.com/assets/img/logo.png
route_is('/admin/*');          // bool — pattern eşleşmesi
back();                        // HTTP_REFERER'a redirect
```

---

**Sonraki:** [Middleware →](middleware.md)
