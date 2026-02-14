<?php

namespace App\Core;

use App\Models\User;

/**
 * Titan Guard - Elite Authentication Engine
 */
class Auth
{
    protected static ?User $user = null;

    /**
     * Attempt to authenticate a user.
     */
    public static function attempt(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            return self::login($user);
        }

        return false;
    }

    /**
     * Log in a user instance.
     */
    public static function login(User $user): bool
    {
        self::$user = $user;
        Session::set('auth_user_id', $user->id);
        Session::set('auth_user', [
            'id' => $user->id,
            'name' => $user->username ?? $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'user'
        ]);

        return true;
    }

    /**
     * Log out the current user.
     */
    public static function logout(): void
    {
        self::$user = null;
        Session::remove('auth_user_id');
        Session::remove('auth_user');
    }

    /**
     * Check if a user is authenticated.
     */
    public static function check(): bool
    {
        return Session::has('auth_user_id');
    }

    /**
     * Get the authenticated user instance.
     */
    public static function user(): ?User
    {
        if (self::$user === null && self::check()) {
            self::$user = User::find(Session::get('auth_user_id'));
        }
        return self::$user;
    }

    /**
     * Get the authenticated user's ID.
     */
    public static function id(): ?int
    {
        return Session::get('auth_user_id');
    }
}
