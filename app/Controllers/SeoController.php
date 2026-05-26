<?php
/**
 * SeoController — sitemap.xml + robots.txt.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Post;
use App\Models\Setting;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $base  = rtrim((string) config('app.url', 'http://localhost'), '/');
        $posts = Post::query()->whereCondition('published', 1)->orderBy('updated_at', 'DESC')->get();

        // Statik public route'lar (manuel liste)
        $static = ['/', '/blog', '/iletisim', '/randevu'];

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($static as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . $u, ENT_XML1) . "</loc>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>" . ($u === '/' ? '1.0' : '0.7') . "</priority>\n";
            $xml .= "  </url>\n";
        }

        foreach ($posts as $p) {
            $loc = htmlspecialchars($base . $p->url(), ENT_XML1);
            $mod = date('c', strtotime((string) ($p->updated_at ?? $p->created_at ?? 'now')));
            $xml .= "  <url>\n";
            $xml .= "    <loc>$loc</loc>\n";
            $xml .= "    <lastmod>$mod</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $r = new Response();
        $r->setHeader('Content-Type', 'application/xml; charset=utf-8')->setContent($xml);
        return $r;
    }

    public function robots(): Response
    {
        $base   = rtrim((string) config('app.url', 'http://localhost'), '/');
        $custom = Setting::get('seo.robots');
        $content = $custom ?: "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\n\nSitemap: $base/sitemap.xml\n";

        $r = new Response();
        $r->setHeader('Content-Type', 'text/plain; charset=utf-8')->setContent($content);
        return $r;
    }
}
