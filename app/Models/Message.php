<?php
/**
 * Message — iletişim formu mesaj kaydı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected static string $table = 'messages';

    protected static array $fillable = [
        'name', 'email', 'phone', 'subject', 'body',
        'ip', 'user_agent', 'is_read', 'read_at',
    ];

    public function markRead(): bool
    {
        return static::updateById($this->attributes['id'] ?? null, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
