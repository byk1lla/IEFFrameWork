<?php
/**
 * Post — blog yazısı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Storage;

class Post extends Model
{
    protected static string $table = 'posts';

    protected static array $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'excerpt', 'content',
        'cover_image', 'published', 'published_at', 'is_featured',
        'seo_title', 'seo_description', 'seo_keywords',
    ];

    public function url(): string
    {
        return '/blog/' . ($this->attributes['slug'] ?? '');
    }

    public function coverUrl(): string
    {
        return Storage::url((string) ($this->attributes['cover_image'] ?? ''));
    }

    public function category(): ?PostCategory
    {
        return isset($this->attributes['category_id'])
            ? PostCategory::find($this->attributes['category_id'])
            : null;
    }

    public function isPublished(): bool
    {
        return (int) ($this->attributes['published'] ?? 0) === 1;
    }

    public function isFeatured(): bool
    {
        return (int) ($this->attributes['is_featured'] ?? 0) === 1;
    }
}
