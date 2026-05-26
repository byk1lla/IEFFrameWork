<?php
/**
 * Inline editör için endpoint'ler:
 *   POST /admin/editor/save   — text/html/image/icon/url/number değer kaydet
 *   POST /admin/editor/upload — görsel/video yükle (ROOT_PATH/assets/img/content)
 *
 * Onur Sürücü Kursu projesinden birebir port.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\SiteContent;

class ContentEditorController extends Controller
{
    public function update()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!SiteContent::isAdminLoggedIn()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            exit;
        }

        $key   = trim($_POST['key'] ?? '');
        $value = $_POST['value'] ?? '';
        $type  = $_POST['type'] ?? 'text';

        if ($key === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'key required']);
            exit;
        }
        if (!in_array($type, ['text', 'html', 'image', 'icon', 'url', 'number'], true)) {
            $type = 'text';
        }

        if ($type === 'text') {
            $value = strip_tags($value);
            $value = preg_replace('/\s+/u', ' ', $value);
            $value = trim($value);
        } elseif ($type === 'html') {
            $value = trim($value);
        } elseif ($type === 'url' || $type === 'image') {
            $value = trim($value);
        } elseif ($type === 'icon') {
            $value = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $value);
            $value = trim($value);
        }

        SiteContent::set($key, (string)$value, $type);

        echo json_encode(['ok' => true, 'key' => $key, 'value' => $value]);
        exit;
    }

    /**
     * Görünüm/Tema ayarlarını kaydet (Setting tablosuna).
     * Site editör sidebar'ından çağrılır.
     */
    public function appearanceSave()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!SiteContent::isAdminLoggedIn()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            exit;
        }

        $key   = trim($_POST['key'] ?? '');
        $value = $_POST['value'] ?? '';

        // İzin verilen anahtarlar
        $allowed = [
            'appearance.logo_url'       => 'string',
            'appearance.logo_dark_url'  => 'string',
            'appearance.favicon_url'    => 'string',
            'appearance.og_default_url' => 'string',
            'appearance.primary_color'  => 'string',
            'appearance.accent_color'   => 'string',
            'appearance.font_family'    => 'string',
            'appearance.footer_text'    => 'string',
            'appearance.copyright_text' => 'string',
        ];

        if (!isset($allowed[$key])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Geçersiz anahtar: ' . $key]);
            exit;
        }

        \App\Models\Setting::set($key, (string) $value, $allowed[$key]);

        echo json_encode(['ok' => true, 'key' => $key, 'value' => $value]);
        exit;
    }

    public function uploadImage()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!SiteContent::isAdminLoggedIn()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            exit;
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'mp4', 'webm', 'mov'];
        if (!in_array($ext, $allowed, true)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Bu dosya tipi izinli değil.']);
            exit;
        }
        if ($file['size'] > 20 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Max 20MB.']);
            exit;
        }

        $dir = PUBLIC_PATH . '/assets/img/content';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $file['name']);
        $filename = time() . '_' . substr(bin2hex(random_bytes(4)), 0, 6) . '_' . $safe;
        $dest = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Dosya kaydedilemedi.']);
            exit;
        }

        $url = '/assets/img/content/' . $filename;
        echo json_encode(['ok' => true, 'url' => $url]);
        exit;
    }
}
