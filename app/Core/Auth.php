<?php
/**
 * Auth — kimlik doğrulama motoru.
 *
 * v2.0 düzeltmesi (bug 3):
 *   - User::where('email', $e)->first()  ← önceki sürümde fatal'dı
 *     (where instance method'tu, static çağrı $this kullanamıyordu).
 *     Yeni Model'de static facade var → çalışıyor.
 *
 * Kullanım:
 *   Auth::attempt($email, $password)   → bool
 *   Auth::login($user)
 *   Auth::user()                        → ?User
 *   Auth::check()                       → bool
 *   Auth::logout()
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    protected static ?User $user = null;

    public static function attempt(string $email, string $password): bool
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();
        if (!$user) return false;

        if (!password_verify($password, (string) $user->password)) {
            return false;
        }
        return self::login($user);
    }

    public static function login(User $user): bool
    {
        self::$user = $user;

        Session::set('auth_user_id', $user->{User::getKey()});
        Session::set('auth_user', [
            'id'    => $user->{User::getKey()},
            'name'  => $user->name ?? $user->username ?? '',
            'email' => $user->email ?? '',
            'role'  => $user->role  ?? 'user',
        ]);
        Session::regenerate();
        return true;
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::remove('auth_user_id');
        Session::remove('auth_user');
        Session::regenerate();
    }

    public static function check(): bool
    {
        return Session::has('auth_user_id');
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function user(): ?User
    {
        if (self::$user === null && self::check()) {
            self::$user = User::find(Session::get('auth_user_id'));
        }
        return self::$user;
    }

    public static function id(): mixed
    {
        return Session::get('auth_user_id');
    }
}
