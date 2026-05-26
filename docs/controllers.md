# Controller'lar

- [Giriş](#giris)
- [Controller Yazma](#controller-yazma)
    - [Temel Controller'lar](#temel-controllerlar)
    - [Tek-Eylemli Controller'lar](#tek-eylemli-controllerlar)
- [Controller Middleware](#controller-middleware)
- [Resource Controller'ları](#resource-controllerlar)
- [Dependency Injection](#dependency-injection)
- [Base Controller'ın Sunduğu Yardımcılar](#base-controllerin-sunduu-yardmclar)
- [Controller Üretme](#controller-uretme)

---

## Giriş

Bütün isteklerin closure ile tanımlanması yerine, ilgili logic'i bir **controller** sınıfına gruplamak isteyebilirsin. Controller'lar HTTP isteklerini işleyen sınıflardır. Birbiriyle ilgili request handling logic'ini tek sınıfta toplar. Örneğin, `UserController` bir kullanıcının görüntülenmesi, oluşturulması, güncellenmesi ve silinmesi gibi gelen tüm istekleri işleyebilir.

Tüm controller'lar varsayılan olarak `app/Controllers/` dizininde bulunur. Admin paneline ait controller'lar ise `app/Controllers/Admin/` alt dizinindedir.

---

## Controller Yazma

### Temel Controller'lar

Aşağıda temel bir controller örneği göreceksin. Controller, `App\Core\Controller` base sınıfından kalıtılmalıdır. Base sınıf, controller'a `view()`, `redirect()`, `json()`, `flash()`, `abortIfInvalidCsrf()` gibi yardımcı method'lar sağlar.

```php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Verilen kullanıcının profilini göster.
     */
    public function show(int $id): Response
    {
        $user = User::find($id);

        return $this->view('user.profile', [
            'user' => $user,
        ]);
    }
}
```

Controller method'una route tanımlayabilirsin:

```php
use App\Controllers\UserController;

Router::get('/user/{id}', 'UserController@show');
```

Gelen istek belirtilen route URI'sıyla eşleştiğinde, `UserController` sınıfındaki `show` method'u çağrılacak ve route parametreleri method'a iletilecek.

> **Tip:** Controller'lar otomatik olarak instantiate edilir — DI container'a kayıt gerekmez. Constructor argümanları yoktur; bağımlılıklar method içinde çözülür.

### Tek-Eylemli Controller'lar

Bir controller eylemi özellikle karmaşıksa, o tek eyleme adanmış bir controller sınıfı oluşturmak uygun olabilir. Bunu başarmak için controller'da tek bir `__invoke` method'u tanımlayabilirsin:

```php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

class ProvisionServer extends Controller
{
    public function __invoke(): Response
    {
        // Sunucu provisioning logic'i...
        return $this->view('servers.provisioned');
    }
}
```

Tek-eylemli controller için route kaydederken method belirtmen gerekmez:

```php
Router::post('/server', 'ProvisionServer');
```

---

## Controller Middleware

[Middleware](middleware.md) en sık route grupları üzerinden atanır. Ancak, controller'ın constructor'ında da middleware atayabilirsin:

```php
public function __construct()
{
    $this->middleware(\App\Middleware\AuthMiddleware::class);
}
```

> Çoğu durumda route grubu middleware'ini tercih et — daha açık ve okunabilir.

---

## Resource Controller'ları

Bir uygulamadaki her veritabanı kaynağını "kaynak" (resource) olarak düşünürsen, her kaynak için tipik olarak aynı eylem setini gerçekleştirirsin. Örneğin, "Photo" ve "Movie" kaynakları olsun; kullanıcılar muhtemelen bunları oluşturuyor, okuyor, güncelliyor ve siliyordur.

IEF Framework'te tek bir komutla resource controller üretmek yoktur, ancak konvansiyonel yapıyı manuel uygulayabilirsin:

| HTTP Method | URI | Action | Route Adı |
|---|---|---|---|
| GET | `/photos` | index | photos.index |
| GET | `/photos/create` | create | photos.create |
| POST | `/photos` | store | photos.store |
| GET | `/photos/{photo}` | show | photos.show |
| GET | `/photos/{photo}/edit` | edit | photos.edit |
| POST | `/photos/{photo}/update` | update | photos.update |
| POST | `/photos/{photo}/delete` | destroy | photos.destroy |

```php
Router::group(['prefix' => '/photos'], function () {
    Router::get ('/',                  'PhotoController@index');
    Router::get ('/create',            'PhotoController@create');
    Router::post('/',                  'PhotoController@store');
    Router::get ('/{id}',              'PhotoController@show');
    Router::get ('/{id}/edit',         'PhotoController@edit');
    Router::post('/{id}/update',       'PhotoController@update');
    Router::post('/{id}/delete',       'PhotoController@destroy');
});
```

> HTML formlar `PUT`/`DELETE` desteklemediği için `update`/`destroy` POST + path olarak tanımlanır. Tercih edersen `<input type="hidden" name="_method" value="PUT">` kullanıp `Router::put(...)` ile tanımlayabilirsin.

---

## Dependency Injection

Framework'ün service container'ı **yoktur** — bu, basitlik için bilinçli bir tasarım kararıdır. Bağımlılıkları doğrudan instantiate eder veya facade benzeri statik erişim kullanırsın:

```php
public function store(): Response
{
    $request = $this->request;                  // base controller'dan
    $name    = $request->input('name');

    User::query()->insert([
        'name'  => $name,
        'email' => $request->input('email'),
    ]);

    // Mail
    (new \App\Services\MailService())->send(
        to:      $request->input('email'),
        subject: 'Hoş Geldin',
        body:    "Merhaba {$name}!"
    );

    return $this->redirect('/dashboard');
}
```

> Servis sınıfların durumsuz (stateless) ise, doğrudan `new`'lemekten çekinme. Bu, container ezberi olmadan iş gören kodun temelidir.

---

## Base Controller'ın Sunduğu Yardımcılar

`App\Core\Controller` aşağıdaki yardımcıları sağlar:

```php
$this->request;                              // App\Core\Request instance
$this->view($template, $data);               // app/Views/$template.php → Response
$this->redirect($url, $status = 302);        // Response (Location header)
$this->json($data, $status = 200);           // JSON Response
$this->flash($key, $message);                // Session flash mesaj
$this->abortIfInvalidCsrf();                 // CSRF token doğrula, geçersizse 419
```

Örnek:

```php
public function submit(): Response
{
    $this->abortIfInvalidCsrf();

    $name  = $this->request->input('name');
    $email = $this->request->input('email');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->flash('error', 'Geçersiz e-posta adresi.');
        return $this->redirect('/iletisim');
    }

    Message::create(['name' => $name, 'email' => $email]);
    $this->flash('success', 'Mesajın bize ulaştı.');

    return $this->redirect('/iletisim/tesekkurler');
}
```

---

## Controller Üretme

Yeni controller iskeletini CLI ile üretebilirsin:

```bash
./ief make:controller PhotoController
```

Bu, `app/Controllers/PhotoController.php` dosyasını standart stub ile oluşturur:

```php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

class PhotoController extends Controller
{
    public function index(): Response
    {
        return $this->view('photo.index');
    }
}
```

Admin alt dizini için:

```bash
./ief make:controller Admin/ReportController
```

---

**Sonraki:** [Request →](requests.md)
