<?php
/**
 * Admin\BlogController — blog yazıları CRUD + AI generate.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Storage;
use App\Core\Validator;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\GroqService;

class BlogController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()->orderBy('created_at', 'DESC')->take(50)->get();
        return $this->view('admin.blog.index', ['posts' => $posts]);
    }

    public function create(): Response
    {
        return $this->view('admin.blog.edit', [
            'post'       => new Post(),
            'categories' => PostCategory::all(),
            'mode'       => 'create',
            'aiReady'    => GroqService::isReady(),
        ]);
    }

    public function store(): Response
    {
        $this->abortIfInvalidCsrf();
        $data = $this->prepareData();
        if (is_string($data)) { $this->flash('error', $data); return $this->redirect('/admin/blog/create'); }

        $data['slug']       = $this->uniqueSlug($data['slug'] ?? Storage::slugify((string) $data['title']));
        $data['user_id']    = Auth::id();
        $data['published']  = (int) (bool) ($this->request->input('published'));
        $data['is_featured']= (int) (bool) ($this->request->input('is_featured'));
        $data['published_at'] = $data['published'] ? date('Y-m-d H:i:s') : null;

        if ($this->request->hasFile('cover')) {
            $data['cover_image'] = Storage::put('blog', $this->request->file('cover'), ['jpg','jpeg','png','webp','gif']);
        } elseif ($manual = trim((string) $this->request->input('cover_image', ''))) {
            $data['cover_image'] = $manual;
        }

        Post::create($data);
        $this->flash('success', 'Yazı oluşturuldu.');
        return $this->redirect('/admin/blog');
    }

    public function edit(string $id): Response
    {
        $post = Post::find($id);
        if (!$post) return $this->notFound();
        return $this->view('admin.blog.edit', [
            'post'       => $post,
            'categories' => PostCategory::all(),
            'mode'       => 'edit',
            'aiReady'    => GroqService::isReady(),
        ]);
    }

    public function update(string $id): Response
    {
        $this->abortIfInvalidCsrf();

        $post = Post::find($id);
        if (!$post) return $this->notFound();

        $data = $this->prepareData();
        if (is_string($data)) { $this->flash('error', $data); return $this->redirect("/admin/blog/$id/edit"); }

        $newPub = (int) (bool) $this->request->input('published');
        $data['published']  = $newPub;
        $data['is_featured']= (int) (bool) $this->request->input('is_featured');
        if ($newPub && !$post->published_at) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($this->request->hasFile('cover')) {
            if ($post->cover_image) Storage::delete((string) $post->cover_image);
            $data['cover_image'] = Storage::put('blog', $this->request->file('cover'), ['jpg','jpeg','png','webp','gif']);
        } elseif ($manual = trim((string) $this->request->input('cover_image', ''))) {
            $data['cover_image'] = $manual;
        }

        Post::updateById($id, $data);
        $this->flash('success', 'Yazı güncellendi.');
        return $this->redirect("/admin/blog/$id/edit");
    }

    public function destroy(string $id): Response
    {
        $this->abortIfInvalidCsrf();
        $post = Post::find($id);
        if ($post && $post->cover_image) Storage::delete((string) $post->cover_image);
        Post::deleteById($id);
        $this->flash('success', 'Yazı silindi.');
        return $this->redirect('/admin/blog');
    }

    /**
     * POST /admin/blog/ai/generate — Groq ile yazı üret (JSON).
     */
    public function aiGenerate(): Response
    {
        if (!$this->validateCsrf()) {
            return $this->json(['ok' => false, 'error' => 'CSRF token geçersiz.'], 419);
        }
        if (!GroqService::isReady()) {
            return $this->json(['ok' => false, 'error' => 'Groq API key tanımlanmamış. Ayarlar → AI sekmesi.'], 400);
        }

        $topic = trim((string) $this->request->input('topic', ''));
        if (mb_strlen($topic) < 6) {
            return $this->json(['ok' => false, 'error' => 'Konu en az 6 karakter olmalı.'], 422);
        }

        try {
            $post = GroqService::generateBlogPost($topic);
            return $this->json(['ok' => true, 'post' => $post]);
        } catch (\Throwable $e) {
            return $this->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── Internal ─────────────────────────────────────────────────
    protected function prepareData(): array|string
    {
        $v = Validator::make($this->request->all(), [
            'title'           => 'required|min:3|max:200',
            'slug'            => 'max:220',
            'excerpt'         => 'max:500',
            'content'         => 'required|min:5',
            'category_id'     => 'integer',
            'seo_title'       => 'max:191',
            'seo_description' => 'max:300',
            'seo_keywords'    => 'max:300',
        ]);
        if ($v->fails()) {
            $errs = $v->errors();
            $first = reset($errs);
            return is_array($first) ? ($first[0] ?? 'Form hatası') : (string) $first;
        }

        return [
            'title'           => trim((string) $this->request->input('title')),
            'slug'            => Storage::slugify((string) ($this->request->input('slug') ?: $this->request->input('title'))),
            'excerpt'         => trim((string) $this->request->input('excerpt', '')) ?: null,
            'content'         => (string) $this->request->input('content'),
            'category_id'     => (int) ($this->request->input('category_id') ?: 0) ?: null,
            'seo_title'       => trim((string) $this->request->input('seo_title', ''))       ?: null,
            'seo_description' => trim((string) $this->request->input('seo_description', '')) ?: null,
            'seo_keywords'    => trim((string) $this->request->input('seo_keywords', ''))    ?: null,
        ];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (Post::query()->whereCondition('slug', $slug)->first()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}
