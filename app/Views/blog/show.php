@extends('layouts.app')

@section('content')
<style>
    .titan-post-stage {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 100px;
    }

    .titan-post-header {
        margin-bottom: 60px;
        text-align: center;
    }

    .titan-post-header h1 {
        font-size: 4rem;
        font-weight: 900;
        letter-spacing: -3px;
        text-transform: uppercase;
        margin-bottom: 20px;
        line-height: 1.1;
    }

    .titan-post-meta {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--purple);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 40px;
    }

    .titan-post-body {
        background: var(--obsidian);
        border: 1px solid var(--border);
        padding: 60px;
        border-radius: 4px;
        font-size: 1.25rem;
        line-height: 2;
        color: var(--text-dim);
        font-weight: 400;
    }

    .titan-post-body p {
        margin-bottom: 30px;
    }

    .back-titan {
        display: inline-block;
        margin-top: 50px;
        font-weight: 900;
        color: var(--cyan);
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.8rem;
    }

    .back-titan:hover {
        text-shadow: 0 0 10px var(--cyan);
    }
</style>

<div class="titan-post-stage">
    <div class="titan-post-header">
        <div class="titan-post-meta">
            {{ date('M d, Y', strtotime($post->created_at ?? 'now')) }} • {{ trans('mission_by') }} {{ $post->author ??
            'UNKNOWN' }}
        </div>
        <h1>{{ $post->title ?? 'NO_TITLE' }}</h1>
    </div>

    <article class="titan-post-body">
        {!! nl2br(htmlspecialchars($post->content ?? '')) !!}
    </article>

    <a href="/blog" class="back-titan">← {{ trans('back_to_blog') }}</a>
</div>
@endsection