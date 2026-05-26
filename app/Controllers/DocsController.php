<?php
/**
 * DocsController — docs/ klasöründeki MD dosyalarını sidebar'lı bir sayfada render eder.
 *
 * GET /docs              → installation.md (giriş)
 * GET /docs/{topic}      → docs/{topic}.md
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use Parsedown;

class DocsController extends Controller
{
    /**
     * Sidebar navigation — Laravel docs benzeri kategorize yapı.
     * Slug → docs/{slug}.md
     */
    protected array $nav = [
        'Prologue' => [
            'release-notes'  => 'Sürüm Notları',
            'upgrade'        => 'Yükseltme Rehberi',
            'contributing'   => 'Katkıda Bulunma',
        ],
        'Başlangıç' => [
            'installation'        => 'Kurulum',
            'configuration'       => 'Konfigürasyon',
            'directory-structure' => 'Dizin Yapısı',
            'deployment'          => 'Dağıtım',
        ],
        'Mimari' => [
            'lifecycle'    => 'Request Yaşam Döngüsü',
            'architecture' => 'Mimari Kararlar',
            'helpers'      => 'Helper Fonksiyonlar',
        ],
        'Temeller' => [
            'routing'      => 'Routing',
            'middleware'   => 'Middleware',
            'csrf'         => 'CSRF Koruması',
            'controllers'  => 'Controller\'lar',
            'requests'     => 'Request',
            'responses'    => 'Response',
            'views'        => 'Views (Blade-lite)',
            'validation'   => 'Validation',
            'errors'       => 'Hata Yönetimi',
            'logging'      => 'Logging',
        ],
        'Veritabanı' => [
            'database'        => 'Başlangıç',
            'queries'         => 'Query & PDO',
            'migrations'      => 'Migration\'lar',
            'schema-builder'  => 'Schema Builder',
            'models'          => 'Modeller (ORM)',
            'seeders'         => 'Seeder\'lar',
        ],
        'Güvenlik' => [
            'authentication' => 'Authentication',
            'authorization'  => 'Authorization (RBAC)',
            'password-reset' => 'Şifre Sıfırlama',
            'sessions'       => 'Session',
            'encryption'     => 'Şifreleme',
            'rate-limiting'  => 'Rate Limiting',
        ],
        'Servisler' => [
            'mail'         => 'Mail',
            'contact'      => 'İletişim Formu',
            'appointments' => 'Randevu',
        ],
        'Frontend' => [
            'assets' => 'Asset\'ler & Tailwind',
            'pwa'    => 'PWA',
            'seo'    => 'SEO',
        ],
        'Admin Paneli' => [
            'admin-panel'  => 'Genel Bakış',
            'site-editor'  => 'Site Editör',
            'settings'     => 'Ayarlar',
            'analytics'    => 'Analytics',
            'blog'         => 'Blog & İçerik',
            'media'        => 'Medya',
            'logs'         => 'Loglar',
            'users'        => 'Kullanıcılar',
        ],
        'AI' => [
            'ai-groq'  => 'Groq Entegrasyonu',
            'blog-ai'  => 'AI ile Blog Üretimi',
        ],
        'İleri' => [
            'cli'         => 'CLI (./ief)',
            'maintenance' => 'Bakım Modu',
            'debug-bar'   => 'Debug Bar',
            'performance' => 'Performans',
            'cache'       => 'Cache',
        ],
    ];

    public function index(): Response
    {
        return $this->show('installation');
    }

    public function show(string $topic = 'installation'): Response
    {
        // Slug güvenliği — sadece alfanümerik + tire
        if (!preg_match('/^[a-z0-9-]+$/i', $topic)) {
            return $this->docsMissing($topic);
        }

        $file = ROOT_PATH . '/docs/' . $topic . '.md';
        if (!is_file($file)) {
            return $this->docsMissing($topic);
        }

        $md   = (string) file_get_contents($file);
        $html = $this->renderMarkdown($md);
        [$title, $headings] = $this->extractMeta($md);

        return $this->view('docs.show', [
            'topic'    => $topic,
            'title'    => $title,
            'html'     => $html,
            'headings' => $headings,
            'nav'      => $this->nav,
        ]);
    }

    protected function renderMarkdown(string $md): string
    {
        $pd = new Parsedown();
        $pd->setSafeMode(false);
        $pd->setBreaksEnabled(false);
        $html = $pd->text($md);

        // İç linkleri docs path'ine çevir: (installation.md) → (/docs/installation)
        $html = preg_replace_callback(
            '/href="([a-z0-9-]+)\.md(#[^"]+)?"/i',
            fn ($m) => 'href="/docs/' . $m[1] . ($m[2] ?? '') . '"',
            $html
        );

        // Başlık id'leri (heading anchor için)
        $html = preg_replace_callback(
            '/<h([2-4])>(.*?)<\/h\1>/',
            function ($m) {
                $slug = $this->slugify(strip_tags($m[2]));
                return "<h{$m[1]} id=\"{$slug}\">{$m[2]}</h{$m[1]}>";
            },
            $html
        );

        return $html;
    }

    /**
     * @return array{0:string,1:array<int,array{level:int,title:string,slug:string}>}
     */
    protected function extractMeta(string $md): array
    {
        $title = 'Dokümantasyon';
        if (preg_match('/^#\s+(.+)$/m', $md, $m)) {
            $title = trim($m[1]);
        }

        $headings = [];
        if (preg_match_all('/^(#{2,3})\s+(.+)$/m', $md, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $headings[] = [
                    'level' => strlen($m[1]),
                    'title' => trim($m[2]),
                    'slug'  => $this->slugify(trim($m[2])),
                ];
            }
        }

        return [$title, $headings];
    }

    protected function slugify(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/\s+/', '-', trim($text));
        $text = mb_strtolower($text);
        return trim($text, '-');
    }

    protected function docsMissing(string $topic): Response
    {
        return $this->view('docs.show', [
            'topic'    => $topic,
            'title'    => 'Bulunamadı',
            'html'     => '<h1>404 — Doküman bulunamadı</h1>'
                        . '<p>Aradığın konu (<code>' . htmlspecialchars($topic) . '</code>) henüz yazılmamış.</p>'
                        . '<p><a href="/docs">← Dokümantasyona dön</a></p>',
            'headings' => [],
            'nav'      => $this->nav,
        ])->setStatusCode(404);
    }
}
