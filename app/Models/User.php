<?php
/**
 * User Model.
 *
 * Kullanım: User::where('email', $e)->first(), User::register([...]),
 *           Auth::attempt($email, $password).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table      = 'users';
    protected static string $primaryKey = 'id';
    protected static bool   $useUuid    = false;

    protected static array $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    protected static array $hidden = ['password'];

    /**
     * Şifre hash'leyerek yeni kullanıcı yarat.
     */
    public static function register(array $data): static
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }
        return self::create($data);
    }

    /**
     * Mevcut kullanıcının rolü verilen role'lerden biri mi.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->attributes['role'] ?? 'user', $roles, true);
    }
}
