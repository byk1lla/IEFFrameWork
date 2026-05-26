# Views (Blade-lite)

- [Giriş](#giris)
- [View Oluşturma](#view-olusturma)
- [View'a Veri İletme](#viewa-veri-iletme)
- [Blade-lite Direktifleri](#blade-lite-direktifleri)
    - [Yazdırma](#yazdrma)
    - [İf / Else](#if-else)
    - [Döngüler](#dongulrer)
    - [Layout & Section](#layout-section)
    - [Include & Each](#include-each)
    - [Raw PHP](#raw-php)
- [CSRF & Form Helper'ları](#csrf-form-helperlar)
- [Stack & Push](#stack-push)
- [Inline Editör Helper'ları](#inline-editor-helperlari)

---

## Giriş

View'lar uygulamanın HTML'ini sunum mantığından ayırmak için kullanılır. View'lar `app/Views/` dizininde saklanır ve `.php` uzantılıdır. IEF Framework, Laravel'in Blade syntax'ına çok yakın **Blade-lite** kendi template engine'ini kullanır. Compile gerekmez — view'lar runtime'da regex tabanlı dönüştürülür.

```blade
<!-- app/Views/welcome.php -->
@extends('layouts.app')

@section('content')
    <h1>Merhaba, {{ $name }}</h1>
@endsection
```

---

## View Oluşturma

Bir view'ı controller'dan döndürmek:

```php
return $this->view('welcome');
return $this->view('user.profile', ['user' => $user]);
```

Helper:

```php
return view('welcome', ['name' => 'Efe']);
```

`'user.profile'` → `app/Views/user/profile.php` dosyasına çözülür.

Bir view'ın var olup olmadığını kontrol etmek için:

```php
if (view_exists('admin.dashboard')) {
    return view('admin.dashboard');
}
```

---

## View'a Veri İletme

İkinci argüman olarak associative array gönder; her anahtar view'da değişken olur:

```php
return view('greeting', ['name' => 'Efe', 'age' => 28]);
```

```blade
<p>Merhaba {{ $name }}, yaşın {{ $age }}.</p>
```

> Tüm view'lara global olarak veri paylaşmak için `app/Core/View.php` içinde `share()` mekanizması yoktur — bunun yerine `layouts/app.php` içinde `@php ... @endphp` bloğunda hazırlayabilirsin.

---

## Blade-lite Direktifleri

### Yazdırma

`{{ }}` ve `{!! !!}` — Laravel'le aynı:

```blade
<p>{{ $user->name }}</p>             <!-- HTML escape edilir -->
<p>{!! $post->body_html !!}</p>       <!-- raw, escape edilmez -->
```

XSS koruması için **her zaman** `{{ }}` kullan. `{!! !!}` sadece zaten güvenli olduğunu bildiğin (kendin sanitize ettiğin) HTML için.

### İf / Else

```blade
@if($user->role === 'admin')
    <p>Hoş geldin, yönetici.</p>
@elseif($user->role === 'editor')
    <p>Editör.</p>
@else
    <p>Kullanıcı.</p>
@endif

@unless($post->published)
    <span class="badge">Taslak</span>
@endunless

@isset($flash)
    <div class="alert">{{ $flash }}</div>
@endisset

@empty($comments)
    <p>Henüz yorum yok.</p>
@endempty
```

### Döngüler

```blade
@foreach($posts as $post)
    <article>
        <h2>{{ $post->title }}</h2>
        <p>{{ $post->excerpt }}</p>
    </article>
@endforeach

@for($i = 0; $i < 10; $i++)
    <li>Item {{ $i }}</li>
@endfor

@while($queue->hasItem())
    @php $item = $queue->next(); @endphp
    <p>{{ $item->name }}</p>
@endwhile
```

`@forelse` (boşsa fallback):

```blade
@forelse($posts as $post)
    <h2>{{ $post->title }}</h2>
@empty
    <p>Henüz yazı yok.</p>
@endforelse
```

### Layout & Section

```blade
<!-- layouts/app.php -->
<!doctype html>
<html>
<head><title>@yield('title') · Site</title></head>
<body>
    <main>@yield('content')</main>
    @yield('scripts')
</body>
</html>
```

```blade
<!-- welcome.php -->
@extends('layouts.app')

@section('title', 'Anasayfa')

@section('content')
    <h1>Hoş geldin</h1>
@endsection

@section('scripts')
    <script>console.log('ready')</script>
@endsection
```

### Include & Each

```blade
@include('partials.header')
@include('partials.alert', ['type' => 'error', 'message' => 'Hata'])

@each('partials.user-card', $users, 'user', 'partials.no-users')
```

### Raw PHP

```blade
@php
    $total = collect($items)->sum('price');
    $tax   = $total * 0.18;
@endphp

<p>Toplam: {{ number_format($total + $tax, 2) }} ₺</p>
```

---

## CSRF & Form Helper'ları

`@csrf` direktifi formlara CSRF token hidden input'u yerleştirir:

```blade
<form method="POST" action="/iletisim">
    @csrf
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <textarea name="message"></textarea>
    <button type="submit">Gönder</button>
</form>
```

Render edildiğinde:

```html
<input type="hidden" name="_csrf_token" value="<rastgele-token>">
```

> Detay: [CSRF →](csrf.md)

---

## Stack & Push

Birden fazla view'dan aynı `@yield`'a katkı vermek için stack:

```blade
<!-- layouts/app.php -->
<head>
    @stack('styles')
</head>
```

```blade
<!-- pages/blog.php -->
@push('styles')
    <link rel="stylesheet" href="/blog.css">
@endpush
```

> Stack desteği kısıtlıdır — kompleks build sistemi yerine layout'un `@yield('head')` alanına partial include'lamak daha temizdir.

---

## Inline Editör Helper'ları

Admin login + `/site-editor` aktif iken sayfa içeriği canlı düzenlenebilir. View'da:

```blade
{!! editable('home.hero.title', 'Hoş geldin', ['tag' => 'h1', 'class' => 'text-5xl']) !!}
{!! editable_image('home.hero.bg', '/assets/img/hero.jpg', ['alt' => 'Hero']) !!}
{!! editable_icon('home.features.icon1', 'fa-solid fa-bolt') !!}
```

Editör modu kapalıyken: düz HTML render.
Editör modu açıkken: `data-editable` attribute'ları + click-to-edit + modal.

> Detay: [Site Editör →](site-editor.md)

---

**Sonraki:** [Validation →](validation.md)
