<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static bool $useUuid = false;

    protected static array $fillable = [
        'username',
        'email',
        'password',
        'role',
        'avatar'
    ];

    /**
     * Create a new user with hashed password.
     */
    public static function register(array $data): ?self
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return self::create($data);
    }
}
