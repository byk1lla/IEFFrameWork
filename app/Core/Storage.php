<?php
/**
 * Storage — dosya kaydetme/silme/url üretme abstraction.
 *
 * Diskler config/filesystems.php yerine sade: public/uploads/<bucket>/
 * dizinine yazar, /uploads/<bucket>/<file> URL'i döner.
 *
 * Kullanım:
 *   $path = Storage::put('media', $request->file('image'));
 *   Storage::delete($path);
 *   echo Storage::url($path);
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Storage
{
    protected static string $publicDir = '';

    public static function bootIfNeeded(): void
    {
        if (self::$publicDir === '') {
            self::$publicDir = PUBLIC_PATH . '/uploads';
        }
        if (!is_dir(self::$publicDir)) @mkdir(self::$publicDir, 0775, true);
    }

    /**
     * $_FILES array'ından bir dosyayı kaydet.
     * Döner: '/uploads/<bucket>/<filename>' — DB'de saklanacak path.
     */
    public static function put(string $bucket, array $file, array $allowed = []): string
    {
        self::bootIfNeeded();
        self::validate($file, $allowed);

        $bucket = preg_replace('/[^a-zA-Z0-9_-]/', '', $bucket) ?: 'misc';
        $dir    = self::$publicDir . '/' . $bucket;
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $base = self::slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        $name = $base . '-' . substr(bin2hex(random_bytes(4)), 0, 6) . ($ext ? '.' . $ext : '');

        $target = $dir . '/' . $name;
        if (!@move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Dosya yüklenemedi.');
        }
        @chmod($target, 0644);

        return '/uploads/' . $bucket . '/' . $name;
    }

    public static function delete(string $path): bool
    {
        self::bootIfNeeded();
        $path = ltrim($path, '/');
        if (!str_starts_with($path, 'uploads/')) return false;
        $abs = PUBLIC_PATH . '/' . $path;
        return is_file($abs) && @unlink($abs);
    }

    public static function url(?string $path): string
    {
        if (!$path) return '';
        return '/' . ltrim($path, '/');
    }

    public static function exists(string $path): bool
    {
        $path = ltrim($path, '/');
        return is_file(PUBLIC_PATH . '/' . $path);
    }

    public static function size(string $path): int
    {
        $path = ltrim($path, '/');
        $abs  = PUBLIC_PATH . '/' . $path;
        return is_file($abs) ? (int) filesize($abs) : 0;
    }

    public static function mime(string $path): string
    {
        $path = ltrim($path, '/');
        $abs  = PUBLIC_PATH . '/' . $path;
        return is_file($abs) ? (mime_content_type($abs) ?: 'application/octet-stream') : '';
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected static function validate(array $file, array $allowed): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Dosya yükleme hatası (kod: ' . ($file['error'] ?? '?') . ').');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Geçersiz yükleme.');
        }
        if ($allowed) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                throw new \RuntimeException('İzin verilmeyen dosya türü: .' . $ext);
            }
        }
    }

    public static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $tr   = ['ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u'];
        $text = strtr($text, $tr);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-') ?: 'file';
    }
}
