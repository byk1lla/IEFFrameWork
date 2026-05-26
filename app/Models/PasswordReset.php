<?php
/**
 * PasswordReset — şifre sıfırlama token kaydı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class PasswordReset extends Model
{
    protected static string $table = 'password_resets';
    protected static bool   $timestamps = false;

    protected static array $fillable = ['email', 'token', 'expires_at', 'used_at', 'ip'];

    /** 60 dakikalık geçerlilikle yeni token oluştur (mevcut aktif token'ı çöp at). */
    public static function generateFor(string $email, ?string $ip = null, int $minutes = 60): string
    {
        $email = strtolower(trim($email));
        $db    = Database::getInstance();

        // Eski (kullanılmamış, süresi geçmemiş) token'ları temizle
        $db->execute("DELETE FROM password_resets WHERE email = ? AND used_at IS NULL", [$email]);

        $token = bin2hex(random_bytes(32));
        $db->execute(
            "INSERT INTO password_resets (email, token, expires_at, ip) VALUES (?, ?, ?, ?)",
            [$email, $token, date('Y-m-d H:i:s', time() + $minutes * 60), $ip]
        );
        return $token;
    }

    /** Geçerli token'ı bul (süresi dolmamış + kullanılmamış). */
    public static function findValid(string $token): ?array
    {
        $row = Database::getInstance()->fetch(
            "SELECT * FROM password_resets WHERE token = ? AND used_at IS NULL AND expires_at > NOW() LIMIT 1",
            [$token]
        );
        return $row ?: null;
    }

    public static function markUsed(int $id): void
    {
        Database::getInstance()->execute(
            "UPDATE password_resets SET used_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}
