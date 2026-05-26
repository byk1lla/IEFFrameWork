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
        if (!is_dir(ROOT_PATH . '/docs')) {
            return $this->docsBundleMissing();
        }
        return $this->show('installation');
    }

    public function show(string $topic = 'installation'): Response
    {
        if (!is_dir(ROOT_PATH . '/docs')) {
            return $this->docsBundleMissing();
        }

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

    /**
     * docs/ klasörü hiç yok (composer paketinden indirilmiş kurulum).
     * Kullanıcıya online doküman linkini göster.
     */
    protected function docsBundleMissing(): Response
    {
        $html = <<<'HTML'
<!doctype html>
<html lang="tr"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dokümantasyon · IEF Framework</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Inter,system-ui,sans-serif;min-height:100vh;background:linear-gradient(135deg,#f0f9ff,#f8fafc,#ecfeff);display:flex;align-items:center;justify-content:center;padding:32px;color:#0f172a}
.card{max-width:560px;text-align:center;background:#fff;border:1px solid #e2e8f0;border-radius:24px;padding:48px 40px;box-shadow:0 8px 32px -8px rgba(15,23,42,.12)}
.icon{display:inline-flex;align-items:center;justify-content:center;width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,#2563eb,#06b6d4);color:#fff;font-size:32px;margin-bottom:22px;box-shadow:0 12px 32px -8px rgba(37,99,235,.4)}
h1{font:800 28px Inter;letter-spacing:-.025em;margin-bottom:10px}
p{font:15px/1.6 Inter;color:#64748b;margin-bottom:24px;max-width:440px;margin-left:auto;margin-right:auto}
.btns{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:8px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;border-radius:12px;font:600 14px Inter;text-decoration:none;transition:all .2s}
.btn-primary{background:linear-gradient(135deg,#2563eb,#06b6d4);color:#fff;box-shadow:0 8px 22px -6px rgba(37,99,235,.45)}
.btn-primary:hover{transform:translateY(-2px)}
.btn-ghost{background:#fff;color:#475569;border:1px solid #e2e8f0}
.btn-ghost:hover{background:#f8fafc;color:#0f172a}
small{display:block;margin-top:28px;font:13px Inter;color:#94a3b8}
small code{background:#f1f5f9;padding:2px 8px;border-radius:4px;font:500 12px monospace;color:#1d4ed8}
</style></head><body>
<div class="card">
  <div class="icon"><i class="fa-solid fa-book-open"></i></div>
  <h1>Dokümantasyon kuruluma dahil edilmedi</h1>
  <p>Bu projeyi Composer paketi olarak indirdiğin için <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font:500 12px monospace;color:#1d4ed8">docs/</code> klasörü dağıtım sürümünde yok. Tüm dokümantasyon online erişilebilir:</p>
  <div class="btns">
    <a href="https://github.com/byk1lla/IEFFrameWork/tree/main/docs" target="_blank" rel="noopener" class="btn btn-primary">
      <i class="fa-brands fa-github"></i> GitHub Docs
    </a>
    <a href="https://iefsoftware.tr" target="_blank" rel="noopener" class="btn btn-ghost">
      <i class="fa-solid fa-globe"></i> iefsoftware.tr
    </a>
    <a href="/" class="btn btn-ghost">
      <i class="fa-solid fa-arrow-left"></i> Anasayfa
    </a>
  </div>
  <small>Dokümanları yerel olarak istiyorsan: <code>git clone https://github.com/byk1lla/IEFFrameWork docs-source</code></small>
</div>
</body></html>
HTML;
        $response = new Response($html);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        return $response;
    }
}
