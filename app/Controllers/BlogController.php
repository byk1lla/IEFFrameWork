<?php
/**
 * BlogController — public blog listesi ve detayı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Post;
use App\Models\PostCategory;

class BlogController extends Controller
{
    public function index(): Response
    {
        $page    = max(1, (int) $this->request->query('page', 1));
        $perPage = 12;

        $posts = Post::query()
            ->whereCondition('published', 1)
            ->orderBy('published_at', 'DESC')
            ->take($perPage)
            ->skip(($page - 1) * $perPage)
            ->get();

        $cats = PostCategory::all();

        return $this->view('blog.index', [
            'posts'      => $posts,
            'categories' => $cats,
            'page'       => $page,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::query()->whereCondition('slug', $slug)->first();
        if (!$post || !$post->isPublished()) {
            return $this->notFound('Yazı bulunamadı.');
        }
        return $this->view('blog.show', [
            'post'     => $post,
            'category' => $post->category(),
        ]);
    }
}
