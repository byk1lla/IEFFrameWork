@extends('layouts.app')

@section('content')
    <div class="stage">
        <div class="hero-section">
            <h1 class="hero-title">DENEYİM <span>MATRİSİ</span></h1>
            <p class="hero-subtitle">TITAN V4 ARCHITECTURE SYSTEM LOGS</p>
        </div>

        <div class="blog-grid">
            @foreach($posts as $post)
                <article class="blog-card"
                    style="padding: 30px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px;">
                    <div
                        style="font-size: 0.7rem; color: var(--cyan); letter-spacing: 2px; font-weight: 800; margin-bottom: 10px;">
                        {{ strtoupper($post->author) }} // {{ $post->created_at }}</div>
                    <h2 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 20px; line-height: 1.1;">{{ $post->title }}
                    </h2>
                    <p style="color: var(--text-dim); font-size: 0.9rem; line-height: 1.6; margin-bottom: 25px;">
                        {{ str_limit($post->content, 200) }}</p>
                    <a href="/blog/{{ $post->id }}"
                        style="color: #fff; text-decoration: none; font-weight: 900; font-size: 0.7rem; letter-spacing: 2px; display: flex; align-items: center; gap: 10px;">
                        READ CORE DATA
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
@endsection