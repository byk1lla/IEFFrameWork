<?php
/**
 * Admin\EditorController — Site editör.
 *
 * /admin/editor          → sayfa seçici (her sayfa için inline edit linki)
 * /admin/editor/blocks   → manuel CRUD (DataTable)
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Models\PageBlock;

class EditorController extends Controller
{
    /** Inline edit edilebilir sayfalar */
    protected const EDITABLE_PAGES = [
        ['url'=>'/',          'page'=>'home',      'title'=>'Anasayfa',  'desc'=>'Hero, özellikler, CTA bölümleri',         'icon'=>'fa-house',           'color'=>'amber'],
        ['url'=>'/blog',      'page'=>'blog',      'title'=>'Blog',      'desc'=>'Blog liste sayfası başlığı, açıklama',    'icon'=>'fa-newspaper',       'color'=>'violet'],
        ['url'=>'/iletisim',  'page'=>'iletisim',  'title'=>'İletişim',  'desc'=>'İletişim formu metinleri ve açıklamalar',  'icon'=>'fa-envelope',        'color'=>'blue'],
        ['url'=>'/randevu',   'page'=>'randevu',   'title'=>'Randevu',   'desc'=>'Randevu sayfası açıklamaları, başlıklar',  'icon'=>'fa-calendar-check',  'color'=>'green'],
    ];

    // ─── Sayfa seçici (yeni index) ──────────────────────────────────
    public function index(): Response
    {
        // Her sayfanın kaç edit'i kaydedildi (counts)
        $counts = [];
        foreach (self::EDITABLE_PAGES as $p) {
            $row = Database::getInstance()->fetch(
                "SELECT COUNT(*) c FROM page_blocks
                 WHERE page = ? OR (page = 'auto' AND block_key LIKE ?)",
                [$p['page'], $p['page'] . '.%']
            );
            $counts[$p['page']] = (int) ($row['c'] ?? 0);
        }

        return $this->view('admin.editor.index', [
            'pages'  => self::EDITABLE_PAGES,
            'counts' => $counts,
        ]);
    }

    // ─── Bloklar (eski DataTable — /admin/editor/blocks) ────────────
    public function blocks(): Response
    {
        $blocks = PageBlock::query()->orderBy('page', 'ASC')->orderBy('block_key', 'ASC')->take(500)->get();
        return $this->view('admin.editor.blocks', [
            'blocks' => $blocks,
            'total'  => count($blocks),
        ]);
    }

    public function create(): Response
    {
        return $this->view('admin.editor.edit', [
            'block' => new PageBlock(),
            'mode'  => 'create',
        ]);
    }

    public function store(): Response
    {
        $this->abortIfInvalidCsrf();

        $page    = trim((string) $this->request->input('page', ''));
        $key     = trim((string) $this->request->input('block_key', ''));
        $type    = (string) $this->request->input('type', 'text');
        $content = (string) $this->request->input('content', '');
        $label   = (string) $this->request->input('label', '') ?: null;

        if ($page === '' || $key === '') {
            $this->flash('error', 'Sayfa ve anahtar zorunludur.');
            return $this->redirect('/admin/editor/blocks/create');
        }
        if (!in_array($type, ['text','html','image','icon'], true)) $type = 'text';

        PageBlock::set($page, $key, $content, $type, $label);
        $this->flash('success', 'Blok kaydedildi.');
        return $this->redirect('/admin/editor/blocks');
    }

    public function edit(string $id): Response
    {
        $block = PageBlock::find($id);
        if (!$block) return $this->notFound();
        return $this->view('admin.editor.edit', ['block' => $block, 'mode' => 'edit']);
    }

    public function update(string $id): Response
    {
        $this->abortIfInvalidCsrf();

        $block = PageBlock::find($id);
        if (!$block) return $this->notFound();

        $type = (string) $this->request->input('type', 'text');
        if (!in_array($type, ['text','html','image','icon'], true)) $type = 'text';

        PageBlock::set(
            (string) $block->page,
            (string) $block->block_key,
            (string) $this->request->input('content', ''),
            $type,
            (string) $this->request->input('label', '') ?: null
        );
        $this->flash('success', 'Blok güncellendi.');
        return $this->redirect("/admin/editor/blocks/$id/edit");
    }

    public function destroy(string $id): Response
    {
        $this->abortIfInvalidCsrf();
        PageBlock::deleteById($id);
        $this->flash('success', 'Blok silindi.');
        return $this->redirect('/admin/editor/blocks');
    }

    // ─── Inline editor endpoint'leri ────────────────────────────────

    public function inlineSave(): Response
    {
        if (!$this->validateCsrf()) return $this->json(['ok' => false, 'error' => 'CSRF token geçersiz.'], 419);

        $key   = trim((string) $this->request->input('key', ''));
        $value = (string) $this->request->input('value', '');
        $type  = (string) $this->request->input('type', 'text');

        if ($key === '' || !str_contains($key, '.')) {
            return $this->json(['ok' => false, 'error' => 'Geçersiz key.'], 422);
        }
        if (!in_array($type, ['text', 'html', 'image', 'icon'], true)) $type = 'text';

        if ($type === 'text') {
            $value = trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '');
        } else {
            $value = trim($value);
        }

        [$page, $blockKey] = array_pad(explode('.', $key, 2), 2, '');
        PageBlock::set($page, $blockKey, $value, $type);

        return $this->json(['ok' => true, 'key' => $key, 'value' => $value]);
    }

    public function inlineUpload(): Response
    {
        if (!$this->validateCsrf()) return $this->json(['ok' => false, 'error' => 'CSRF'], 419);
        if (!$this->request->hasFile('file')) {
            return $this->json(['ok' => false, 'error' => 'Dosya yok.'], 400);
        }
        try {
            $path = \App\Core\Storage::put('editor', $this->request->file('file'), ['jpg','jpeg','png','webp','gif','svg']);
            return $this->json(['ok' => true, 'url' => $path]);
        } catch (\Throwable $e) {
            return $this->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
