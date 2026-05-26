<?php
/**
 * PwaController — manifest.webmanifest (dinamik, Settings'ten okur).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Setting;

class PwaController extends Controller
{
    public function manifest(): Response
    {
        $appName = (string) (Setting::get('general.site_name') ?: config('app.name', 'IEF Framework'));
        $short   = mb_substr(preg_replace('/\s+/', '', $appName) ?: 'IEF', 0, 12);

        $manifest = [
            'name'             => $appName,
            'short_name'       => $short,
            'description'      => (string) (Setting::get('general.site_tagline') ?: 'Modern PHP framework.'),
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#ffffff',
            'theme_color'      => '#1e40af',
            'lang'             => (string) config('app.locale', 'tr'),
            'icons' => [
                ['src' => '/assets/img/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => '/assets/img/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];

        $r = new Response();
        $r->setHeader('Content-Type', 'application/manifest+json; charset=utf-8')
          ->setContent(json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $r;
    }
}
