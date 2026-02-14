<?php

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected static string $table = 'posts';

    protected static array $fillable = [
        'title',
        'slug',
        'content',
        'author'
    ];

    /**
     * Helper to get featured posts for the elite showcase
     */
    public static function getLatest(int $limit = 10)
    {
        return self::query()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }
}
