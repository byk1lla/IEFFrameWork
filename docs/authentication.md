# Authentication

- [Giriş](#giris)
- [Titan Guard](#titan-guard)
- [Login & Logout](#login-logout)
- [Authenticated User](#authenticated-user)
- [Şifre Hashing](#sifre-hashing)
- [Roller](#roller)
- [İlk Admin'i Oluşturma](#ilk-admini-olusturma)
- [Şifre Sıfırlama](#sifre-sifirlama)

---

## Giriş

IEF, **Titan Guard** adlı kendi kimlik doğrulama sistemini barındırır. Konfigürasyon dosyasız çalışır, session-tabanlıdır ve `App\Core\Auth` üzerinden basit bir API sunar.

> Stateless API token authentication (Sanctum/Passport benzeri) henüz built-in değildir. Cookie + session yaklaşımı tüm web ihtiyaçlarını karşılar.

---

## Titan Guard

Authentication zaten yapılandırılmıştır. Tablonun (`users`) yapısı migration'da tanımlı:

```php
Schema::create('users', function (Blueprint $t) {
    $t->id();
    $t->string('name', 100);
    $t->string('email', 191)->unique();
    $t->string('password');
    $t->string('role', 32)->default('user');
    $t->string('avatar')->nullable();
    $t->timestamp('email_verified_at')->nullable();
    $t->timestamps();
});
```

`User` modeli `App\Models\User`'dadır.

---

## Login & Logout

### Login

```php
use App\Core\Auth;
use App\Models\User;

public function login(): Response
{
    $this->abortIfInvalidCsrf();
    $email = $this->request->input('email');
    $pass  = $this->request->input('password');

    $user = User::where('email', $email)->first();
    if (!$user || !password_verify($pass, $user->password)) {
        $this->flash('error', 'E-posta veya şifre hatalı.');
        return $this->redirect('/login');
    }

    Auth::login($user);
    return $this->redirect('/admin');
}
```

`Auth::login()`:
- Session'a `auth_user_id` ve `auth_user` (id/name/email/role) yazar
- Session ID'yi yeniler (session fixation koruması)

### Logout

```php
public function logout(): Response
{
    Auth::logout();
    return $this->redirect('/');
}
```

---

## Authenticated User

```php
Auth::check();          // bool — giriş yapmış mı
Auth::guest();          // !check()
Auth::id();             // user id veya null
Auth::user();           // User modeli veya null
```

Helper'lar:

```php
auth_check();
auth_id();
auth();   // ['id' => ..., 'name' => ..., 'email' => ..., 'role' => ...]
```

View'da:

```blade
@if(Auth::check())
    <p>Hoş geldin, {{ Auth::user()->name }}.</p>
    <a href="/logout">Çıkış</a>
@else
    <a href="/login">Giriş yap</a>
@endif
```

---

## Şifre Hashing

`password_hash()` ile bcrypt — PHP'nin built-in fonksiyonları:

```php
$hash = password_hash($plain, PASSWORD_BCRYPT);
```

Doğrulama:

```php
if (password_verify($plain, $user->password)) {
    // doğru
}
```

> Bcrypt cost'u varsayılan 10'dur. Production'da CPU'na göre 12 önerilir: `password_hash($p, PASSWORD_BCRYPT, ['cost' => 12])`.

---

## Roller

Standart roller:

| Role | Açıklama |
|---|---|
| `superadmin` | Her şeye erişim — sistem sahibi |
| `admin` | Admin paneli + içerik yönetimi |
| `editor` | Sadece içerik düzenleme |
| `user` | Standart kullanıcı (admin yok) |

Kontrol:

```php
$user = Auth::user();
if ($user && in_array($user->role, ['superadmin', 'admin'])) {
    // Admin işlemi
}
```

Helper:

```php
if (auth() && in_array(auth()['role'], ['superadmin', 'admin'])) {
    // ...
}
```

> Daha detaylı role-based yetkilendirme için: [Authorization →](authorization.md)

---

## İlk Admin'i Oluşturma

İnteraktif CLI komutu — şifre gizli istenir, terminal history'sine düşmez:

```bash
./ief user:create
```

```
İsim: Efe
E-posta: efe@example.com
Rol (superadmin|admin|editor|user) [superadmin]:
Şifre: ********
Şifre (tekrar): ********
✓ Kullanıcı oluşturuldu: efe@example.com (superadmin).
```

> Default seed dosyası **yoktur** — production'da her zaman bu komutla manuel oluştur.

---

## Şifre Sıfırlama

Token-tabanlı şifre sıfırlama hazır gelir. Akış:

1. `/sifre-sifirla` — e-posta gir
2. Sisteme kayıtlıysa: rastgele token üretilir, `password_resets` tablosuna yazılır, e-posta gönderilir
3. Linke tıklayan kullanıcı: `/sifre-sifirla/{token}` — yeni şifre belirler
4. Token bir kez kullanılır, 60 dakika içinde geçerli

> Detay: [Şifre Sıfırlama →](password-reset.md)

---

**Sonraki:** [Authorization (RBAC) →](authorization.md)
