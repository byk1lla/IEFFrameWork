<?php
/**
 * Media — yüklenen dosya kaydı (görsel, doküman vs.).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Storage;

class Media extends Model
{
    protected static string $table = 'media';

    protected static array $fillable = [
        'disk', 'bucket', 'path', 'original_name', 'mime', 'size', 'alt', 'uploaded_by',
    ];

    public function url(): string
    {
        return Storage::url((string) $this->attributes['path'] ?? '');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) ($this->attributes['mime'] ?? ''), 'image/');
    }

    public function humanSize(): string
    {
        $bytes = (int) ($this->attributes['size'] ?? 0);
        if ($bytes < 1024)        return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }
}
