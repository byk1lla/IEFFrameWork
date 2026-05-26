# Authorization (RBAC)

- [Giriş](#giris)
- [Rol Tabanlı Kontrol](#rol-tabanli-kontrol)
- [Middleware ile Korumak](#middleware-ile-korumak)
- [View'da Kontrol](#viewda-kontrol)
- [Özel Policy](#ozel-policy)

---

## Giriş

IEF'in role-based access control (RBAC) sistemi basittir — `users.role` sütununda string bir değer (superadmin, admin, editor, user). Kompleks policy class'ları yerine, kontrolleri doğrudan controller/view'da yaparsın.

---

## Rol Tabanlı Kontrol

```php
use App\Core\Auth;

if (Auth::check() && Auth::user()->role === 'superadmin') {
    // Sadece super admin
}

// Birden fazla rol:
if (in_array(Auth::user()?->role, ['superadmin', 'admin'], true)) {
    // ...
}
```

Helper:

```php
function can_edit_content(): bool {
    $u = auth();
    return $u && in_array($u['role'], ['superadmin', 'admin', 'editor'], true);
}
```

---

## Middleware ile Korumak

Tüm `/admin/*` rotaları `AuthMiddleware` ile korunur (login zorunlu). Rol bazlı middleware için:

```php
// app/Middleware/EnsureSuperAdminMiddleware.php
namespace App\Middleware;

class EnsureSuperAdminMiddleware
{
    public function handle($request, $params = []): bool
    {
        if (!\App\Core\Auth::check() || \App\Core\Auth::user()->role !== 'superadmin') {
            abort(403, 'Yalnızca süper yönetici.');
            return false;
        }
        return true;
    }
}
```

Kullan:

```php
Router::group([
    'prefix' => '/admin/system',
    'middleware' => [AuthMiddleware::class, EnsureSuperAdminMiddleware::class],
], function () {
    Router::get('/users', 'Admin\UserController@index');
});
```

---

## View'da Kontrol

```blade
@if(auth_check() && in_array(auth()['role'], ['superadmin', 'admin']))
    <a href="/admin">Yönetim</a>
@endif

@if(auth() && auth()['role'] === 'superadmin')
    <a href="/admin/system" class="text-red-600">Sistem</a>
@endif
```

---

## Özel Policy

Karmaşık iş kuralları için modelde policy method:

```php
class Post extends Model
{
    public function canEdit(?array $user): bool
    {
        if (!$user) return false;
        if (in_array($user['role'], ['superadmin', 'admin'], true)) return true;
        if ($user['role'] === 'editor' && $this->user_id === $user['id']) return true;
        return false;
    }
}
```

Kullan:

```php
$post = Post::find($id);
if (!$post->canEdit(auth())) {
    abort(403);
}
```

---

**Sonraki:** [Şifre Sıfırlama →](password-reset.md)
