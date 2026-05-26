@extends('layouts.app')

@section('title', $post->title)

@section('content')
<article class="max-w-3xl mx-auto px-6 lg:px-8 pt-16 pb-24">

    {{-- Geri --}}
    <a href="/blog" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-8 transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i> Tüm yazılar
    </a>

    {{-- Header --}}
    <header class="mb-10">
        <div class="flex items-center gap-3 text-[11.5px] text-slate-400 font-semibold tracking-widest uppercase mb-4">
            @if($category)
                <a href="/blog" class="text-brand-700 hover:text-brand-900">{{ $category->name }}</a>
                <span class="text-slate-300">·</span>
            @endif
            <span><i class="fa-regular fa-calendar text-[10px] mr-1"></i>{{ format_date($post->published_at ?? $post->created_at) }}</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter leading-[1.1] text-slate-900 mb-5">{{ $post->title }}</h1>
        @if($post->excerpt)
            <p class="text-lg text-slate-600 leading-relaxed">{{ $post->excerpt }}</p>
        @endif
    </header>

    {{-- Cover --}}
    @if($post->cover_image)
        <figure class="mb-10 -mx-4 md:mx-0">
            <img src="{{ $post->coverUrl() }}" alt="{{ e($post->title) }}" class="w-full max-h-[480px] object-cover rounded-2xl shadow-soft">
        </figure>
    @endif

    {{-- Content --}}
    <div class="prose prose-slate max-w-none text-[16px] leading-[1.75] text-slate-700">
        {!! nl2br(e($post->content)) !!}
    </div>

    {{-- Footer / share --}}
    <footer class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-between flex-wrap gap-4">
        <a href="/blog" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-brand-700 transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i> Tüm yazılara dön
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 mr-1">Paylaş:</span>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url($post->url())) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 hover:border-slate-900 text-slate-600 hover:text-slate-900 rounded-md transition" title="X / Twitter">
                <i class="fa-brands fa-x-twitter text-[14px]"></i>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url($post->url())) }}" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 hover:border-slate-900 text-slate-600 hover:text-slate-900 rounded-md transition" title="LinkedIn">
                <i class="fa-brands fa-linkedin-in text-[14px]"></i>
            </a>
            <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . url($post->url())) }}" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 hover:border-slate-900 text-slate-600 hover:text-slate-900 rounded-md transition" title="WhatsApp">
                <i class="fa-brands fa-whatsapp text-[14px]"></i>
            </a>
        </div>
    </footer>
</article>
@endsection
