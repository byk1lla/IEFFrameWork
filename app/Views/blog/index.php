@extends('layouts.app')

@section('title', 'Blog')

@section('content')

{{-- ═══ Hero ═══════════════════════════════════════════════════════ --}}
<section class="relative pt-24 pb-12 px-6 lg:px-8 border-b border-slate-100">
    <div class="absolute inset-0 -z-10 [background-image:linear-gradient(to_right,rgb(15_23_42_/_0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42_/_0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_70%)]"></div>
    <div class="max-w-3xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-5 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-700 shadow-soft">
            <i class="fa-solid fa-newspaper text-brand-700 text-[11px]"></i>
            Blog · {{ count($posts) }} yazı
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter leading-tight mb-4">
            Düşünceler, <span class="bg-gradient-to-r from-brand-900 via-brand-600 to-accent-500 bg-clip-text text-transparent">notlar</span> ve güncellemeler.
        </h1>
        <p class="text-base text-slate-600 max-w-xl mx-auto">Framework gelişimi, yeni özellikler ve modern PHP üzerine yazılar.</p>
    </div>
</section>

{{-- ═══ Grid ═══════════════════════════════════════════════════════ --}}
<section class="py-16 px-6 lg:px-8">
    @if(!$posts)
        <div class="max-w-2xl mx-auto text-center py-20 px-6 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
            <i class="fa-solid fa-pen-nib text-4xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-bold mb-2">Henüz yazı yok.</h3>
            <p class="text-sm text-slate-500">Yakında ilk yazı yayında olacak.</p>
        </div>
    @else
        <div class="max-w-6xl mx-auto grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <article class="group bg-white border border-slate-100 hover:border-brand-200 rounded-xl overflow-hidden hover:shadow-soft transition-all">
                    @if($post->cover_image)
                        <a href="{{ $post->url() }}" class="block aspect-[16/10] bg-slate-100 overflow-hidden">
                            <img src="{{ $post->coverUrl() }}" alt="{{ e($post->title) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        </a>
                    @else
                        <a href="{{ $post->url() }}" class="block aspect-[16/10] bg-gradient-to-br from-brand-50 to-accent-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-newspaper text-5xl text-brand-200"></i>
                        </a>
                    @endif
                    <div class="p-6">
                        <div class="text-[11px] text-slate-400 font-semibold tracking-widest uppercase mb-2">
                            <i class="fa-regular fa-calendar text-[10px] mr-1"></i>
                            {{ format_date($post->published_at ?? $post->created_at) }}
                        </div>
                        <h3 class="text-lg font-bold tracking-tight mb-2 leading-tight">
                            <a href="{{ $post->url() }}" class="text-slate-900 hover:text-brand-700 transition">{{ $post->title }}</a>
                        </h3>
                        @if($post->excerpt)
                            <p class="text-sm text-slate-600 leading-relaxed">{{ str_limit($post->excerpt, 130) }}</p>
                        @endif
                        <a href="{{ $post->url() }}" class="inline-flex items-center gap-1.5 mt-4 text-sm font-semibold text-brand-700 hover:text-brand-900 group/link">
                            Devamını oku <i class="fa-solid fa-arrow-right text-[10px] group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

@endsection
