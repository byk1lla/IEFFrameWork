@extends('layouts.app')

@section('content')
<style>
    .titan-page-header {
        margin-bottom: 80px;
    }

    .titan-page-header h1 {
        font-size: 5rem;
        font-weight: 900;
        letter-spacing: -4px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .titan-page-header h1 span {
        color: var(--purple);
        text-shadow: var(--glow);
    }

    .titan-page-header p {
        font-size: 1.2rem;
        color: var(--text-dim);
        font-weight: 500;
    }

    .blog-grid {
        display: flex;
        flex-direction: column;
        gap: 40px;
        padding-bottom: 100px;
    }

    .blog-post-card {
        background: var(--obsidian);
        border: 1px solid var(--border);
        padding: 50px;
        border-radius: 4px;
        transition: all 0.3s;
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 40px;
    }

    .blog-post-card:hover {
        border-color: var(--purple);
        box-shadow: var(--glow);
        transform: translateX(10px);
    }

    .post-meta-titan {
        text-align: right;
    }

    .p-date {
        display: block;
        font-size: 0.75rem;
        font-weight: 900;
        color: var(--cyan);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .p-author {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-dim);
    }

    .post-content-titan h2 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: -1px;
        line-height: 1.1;
    }

    .post-content-titan p {
        color: var(--text-dim);
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 30px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .read-titan {
        font-size: 0.8rem;
        font-weight: 900;
        color: var(--purple);
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .read-titan:hover {
        text-shadow: var(--glow);
    }

    @media (max-width: 900px) {
        .blog-post-card {
            grid-template-columns: 1fr;
            padding: 40px;
        }

        .post-meta-titan {
            text-align: left;
        }
    }
</style>

<div class="titan-page-header">
    <h1>Titan<span>Blog</span></h1>
    <p>{{ trans('blog_subtitle') }}</p>
</div>

<div class="blog-grid">
    @foreach($posts as $post)
    <div class="blog-post-card">
        <div class="post-meta-titan">
            <span class="p-date">{{ date('M d, Y', strtotime($post->created_at ?? 'now')) }}</span>
            <span class="p-author">{{ $post->author }}</span>
        </div>
        <div class="post-content-titan">
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->content ?? trans('blog_desc_placeholder') }}</p>
            <a href="/blog/{{ $post->id ?? '' }}" class="read-titan">{{ trans('read_more') }} →</a>
        </div>
    </div>
    @endforeach
</div>
@endsection