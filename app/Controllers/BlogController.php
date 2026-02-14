<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $posts = $db->fetchAll("SELECT * FROM posts");

            return $this->view('blog.index', [
                'title' => 'Blog | IEF Framework',
                'posts' => array_map(fn($item) => new Post($item), $posts)
            ]);
        } catch (\Throwable $t) {
            return "Blog Debug Error: " . $t->getMessage() . " in " . $t->getFile() . " on line " . $t->getLine();
        }
    }

    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $this->notFound('Blog yazısı bulunamadı.');
        }

        return $this->view('blog.show', [
            'title' => $post->title . ' | IEF Blog',
            'post' => $post
        ]);
    }

    private function seed()
    {
        $db = \App\Core\Database::getInstance();
        $db->execute("DELETE FROM posts");
        // $db->execute("DELETE FROM sqlite_sequence WHERE name='posts'");

        $posts = [
            [
                'Titan V4 Architecture: The Reflection Secret',
                'titan-v4-architecture-reflection-secret',
                'The backbone of Titan V4 is its intelligent use of the PHP Reflection API. Unlike traditional frameworks that require manual dependency mapping, Titan analyzes your Controller methods at runtime. If it detects a `Request` type-hint, it automatically injects the singleton instance, ensuring zero-configuration execution and maximum developer velocity.',
                'Core Architect'
            ],
            [
                'Obsidian ORM: Deep Dive into Lazy Query Building',
                'obsidian-orm-lazy-query-building',
                'Obsidian ORM implements a \"Lazy Building\" mechanism known as the \"Zil çalana kadar\" (until the bell rings) pattern. Every `where()`, `orderBy()`, and `limit()` call only modifies the internal state of the query object in memory. The actual SQL construction and PDO execution are deferred until the final moment—when `get()` or `first()` is called, ensuring minimal database overhead.',
                'TitanCore'
            ],
            [
                'Directive Compilation: From @Blade to Pure PHP',
                'directive-compilation-blade-to-pure-php',
                'Titan Engine doesn\'t just parse templates; it compiles them into a permanent state. Using advanced PCRE regex patterns, the engine translates high-level directives like `@if` or `{{ $var }}` into optimized PHP code. This compiled version is what gets executed via `eval()`, providing near-native performance while maintaining the elegance of Blade-lite syntax.',
                'IEF Architect'
            ],
            [
                'Global Matrix: Achieving 100% Localization',
                'global-matrix-achieving-100-localization',
                'In V4, localization is not an afterthought—it is a core service node. The `Lang::load()` mechanism merges your session-persistent locale with the language matrix, allowing the `trans()` helper to provide instantaneous translations. Our new documentation reflects this depth, ensuring that elite applications can serve a global audience with 0% language mixing.',
                'MatrixDev'
            ]
        ];

        foreach ($posts as $post) {
            $db->execute("INSERT INTO posts (title, slug, content, author) VALUES (?, ?, ?, ?)", $post);
        }
    }
}
