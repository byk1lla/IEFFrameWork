# Helper Fonksiyonlar

- [Giriş](#giris)
- [URL](#url)
- [View & Response](#view-response)
- [Auth](#auth)
- [Session & Flash](#session-flash)
- [Config](#config)
- [String](#string)
- [Array](#array)
- [Tarih & Sayı](#tarih-say)
- [Inline Editör](#inline-editor)
- [Çeşitli](#cesitli)

---

## Giriş

IEF, `app/Helpers/helpers.php` dosyasında çok sayıda global fonksiyon sunar. Bunlar her yerden çağrılabilir — controller, view, model.

> Tüm helper'lar `if (!function_exists(...))` ile sarılıdır; kendi global fonksiyonlarınla çakışma olmaz.

---

## URL

```php
url('/blog');                  // tam URL: https://example.com/blog
asset('img/logo.png');         // https://example.com/assets/img/logo.png
route_is('/admin/*');          // pattern eşleşmesi (bool)
redirect('/home');             // header('Location: ...'); exit
back();                        // HTTP_REFERER'a redirect
back('/fallback');             // referer yoksa fallback
```

---

## View & Response

```php
view('welcome', ['name' => 'Efe']);   // Response döner
view_exists('admin.dashboard');        // bool
json(['ok' => true]);                  // application/json
abort(404, 'Bulunamadı');              // exception fırlat, error sayfası
abort_unless($user, 401);              // koşul false ise abort
abort_if($banned, 403);                // koşul true ise abort
```

---

## Auth

```php
auth();                        // mevcut user array veya null
auth_id();                     // user id veya null
auth_check();                  // bool — giriş yapmış mı
```

> Detay: [Authentication →](authentication.md)

---

## Session & Flash

```php
session('key');                // alias: Session::get
session('key', 'default');
session(['key' => 'value']);   // set
flash('success');              // okuyup sil
flash('success', 'Tamam');     // yaz
old('email');                  // son hatalı form'dan
csrf_token();                  // mevcut CSRF token
```

---

## Config

```php
config('app.name');
config('app.debug', false);
config(['app.timezone' => 'UTC']);  // runtime set
```

---

## String

```php
e($string);                    // htmlspecialchars (XSS escape)
str_slug('Merhaba Dünya');     // 'merhaba-dunya'
str_limit($text, 100);         // truncate
str_random(16);                // rastgele 16 karakter
```

---

## Array

```php
data_get($array, 'user.profile.name', 'default');   // nested erişim
data_set($array, 'user.role', 'admin');             // nested set
array_only($array, ['id', 'name']);                 // sadece bu key'ler
array_except($array, ['password']);                 // bu key'leri hariç tut
```

---

## Tarih & Sayı

```php
format_date($timestamp);                   // '24.05.2026 14:30'
format_date('2026-05-24', 'd F Y');        // '24 Mayıs 2026'
human_date('2026-05-24 12:00');            // '2 saat önce' / '3 gün önce'
format_money(1500.50);                     // '1.500,50 ₺'
format_size(1048576);                      // '1.00 MB'
```

---

## Inline Editör

```blade
{!! editable('home.title', 'Hoş geldin', ['tag' => 'h1', 'class' => 'text-4xl']) !!}
{!! editable_image('home.hero', '/img/default.jpg', ['alt' => 'Hero']) !!}
{!! editable_icon('home.icon', 'fa-solid fa-bolt') !!}

{{-- Onur tarzı content() — site_content tablosu --}}
{!! content('home.lead', 'Lead text', ['tag' => 'p']) !!}
{!! content_image('home.bg') !!}
{!! content_icon('home.icon1') !!}
```

> Detay: [Site Editör →](site-editor.md)

---

## Çeşitli

```php
dd($var, $var2);                       // dump & die (debug)
dump($var);                            // dump (devam et)
collect([1,2,3])->sum();               // basit collection helper
env('NOT_USED', 'fallback');           // .env kullanmıyoruz, sadece $_ENV okur
```

---

**Sonraki:** [Hata Yönetimi →](errors.md)
