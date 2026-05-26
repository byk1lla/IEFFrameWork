<?php
/**
 * Admin\LogController — storage/logs altındaki günlük log dosyalarını listeler/gösterir.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;

class LogController extends Controller
{
    public function index(): Response
    {
        $dir   = STORAGE_PATH . '/logs';
        $files = is_dir($dir) ? array_filter(scandir($dir) ?: [], fn ($f) => str_ends_with($f, '.log')) : [];

        // Yeniden eskiye sırala
        rsort($files);

        $current  = (string) $this->request->query('file', $files[0] ?? '');
        $content  = '';
        $size     = 0;
        $isSafe   = preg_match('/^[a-zA-Z0-9._-]+\.log$/', $current) === 1 && in_array($current, $files, true);

        if ($current !== '' && $isSafe) {
            $path = $dir . '/' . $current;
            if (is_file($path)) {
                $size = filesize($path) ?: 0;
                // Çok büyükse sadece son 200 KB
                $maxBytes = 200 * 1024;
                if ($size > $maxBytes) {
                    $fp = @fopen($path, 'r');
                    if ($fp) {
                        fseek($fp, -$maxBytes, SEEK_END);
                        $content = fread($fp, $maxBytes) ?: '';
                        fclose($fp);
                        $content = "[…dosyanın yalnızca son " . round($maxBytes/1024) . " KB'si gösteriliyor…]\n\n" . $content;
                    }
                } else {
                    $content = (string) file_get_contents($path);
                }
            }
        }

        return $this->view('admin.logs.index', [
            'files'   => array_values($files),
            'current' => $isSafe ? $current : '',
            'content' => $content,
            'size'    => $size,
        ]);
    }
}
