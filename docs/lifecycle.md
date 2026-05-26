# Request Yaşam Döngüsü

Her HTTP isteği aşağıdaki adımlarla işlenir:

```
Tarayıcı
   │
   ▼
public/.htaccess (Apache)  ya da  nginx try_files
   │  ↓ tüm istekleri index.php'ye yönlendir
   ▼
index.php
   │
   ├── 1. Sabitler (ROOT_PATH, APP_PATH, ...)
   ├── 2. php -S için public/assets/* manuel servis (varsa exit)
   ├── 3. composer autoload
   ├── 4. config/app.php ön-yükle (timezone, encoding)
   ├── 5. ExceptionHandler::setDebug(...)
   │
   ▼
new App()->run()                ← app/Core/App.php
   │
   ├── Session::start()
   ├── Lang::load()
   ├── isUnderMaintenance() ──► EVET ──► 503 + bakım sayfası, exit
   │       └── Admin login + /admin/* path'leri bypass
   │
   ├── require config/routes.php  ← Router::get/post/group ile route'lar kayıtlanır
   │
   └── Router::dispatch()
         │
         ├── Path & method match
         ├── Middleware'ler çalışır (varsa) — AuthMiddleware vs.
         │       └── Başarısızsa redirect veya 403
         │
         ├── Controller@method invoke
         │       │
         │       ├── Request injekte edilir
         │       └── Response döner (Response, string, array, view)
         │
         └── Response gönderilir (HTML/JSON/redirect/download)
   │
   ▼
fastcgi_finish_request()  (varsa — client'a yanıt gönderilir)
   │
   ▼
AnalyticsService::record()  ← arka planda istek loglanır
```

## Adım Adım

### 1. `index.php` (Tek Entry Point)

Tüm istekler `index.php`'ye düşer. Bu dosya:
- Sabitleri tanımlar (`ROOT_PATH`, `APP_PATH`, `CONFIG_PATH`, `PUBLIC_PATH`, vs.)
- `php -S` kullanıldığında `public/assets/*` ve `public/uploads/*` yollarındaki statik dosyaları kendisi servis eder (default `php -S` router'ı bu yolları bilmiyor)
- Composer autoload'u yükler
- `config/app.php`'yi okur, `date_default_timezone_set` ve `mb_internal_encoding('UTF-8')` yapar
- `App::run()`'ı try-catch içinde çağırır; yakalanmayan exception'lar `ExceptionHandler::handle()`'a düşer

### 2. `App::run()`

`app/Core/App.php`:

```php
public function run(): void
{
    Session::start();
    Lang::load();

    if ($this->isUnderMaintenance()) {
        $this->serveMaintenancePage();
        return;
    }

    require CONFIG_PATH . '/routes.php';
    Router::dispatch();
}
```

### 3. Bakım Modu

- `Setting::get('general.maintenance')` veya `config('app.maintenance.enabled')` true ise:
  - `/admin/*`, `/login`, `/logout`, `/sifre-sifirla*` path'leri her zaman erişilebilir
  - Admin login olmuş kullanıcı bypass eder
  - Diğer herkese `app/Views/errors/maintenance.php` (503 status) gösterilir

> Detay: [Bakım Modu →](maintenance.md)

### 4. Router

`config/routes.php` yüklenir. İçinde:

```php
Router::get('/', 'WelcomeController@index');
Router::post('/login', 'AuthController@login');
Router::group(['prefix' => '/admin', 'middleware' => AuthMiddleware::class], function () {
    Router::get('/', 'AdminController@index');
});
```

`Router::dispatch()`:
- Mevcut URL + method'a uyan route'u bulur
- Middleware'leri sırayla çalıştırır (ilki false dönerse 403)
- Controller'ı instanstiate edip method'unu çağırır
- Path parametrelerini ({slug}, {id}) method argümanı olarak geçer
- Dönen `Response` nesnesini HTTP'ye yazar

### 5. Response

Bir Controller method'u şunlardan birini döndürebilir:

| Tip | Davranış |
|---|---|
| `Response` | Direkt yazılır |
| `string` | `text/html` olarak yazılır |
| `array` | JSON olarak yazılır (`Content-Type: application/json`) |
| `null` | Boş 200 |

Helper'lar:

```php
return $this->view('welcome', ['name' => 'Efe']);
return $this->redirect('/dashboard');
return $this->json(['ok' => true]);
```

### 6. Post-Response: Analytics

`index.php` sonunda — response gönderildikten **sonra** çalışır:

```php
if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();

try {
    (new AnalyticsService())->record(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
        http_response_code(),
        (int) round((microtime(true) - $_iefStart) * 1000)
    );
} catch (\Throwable $e) { /* sessiz */ }
```

`fastcgi_finish_request()` PHP-FPM altında istemciye yanıtı gönderir ve script'i arka planda çalıştırmaya devam eder — analytics kayıt yanıt süresini etkilemez.

---

**Sonraki:** [Mimari →](architecture.md)
