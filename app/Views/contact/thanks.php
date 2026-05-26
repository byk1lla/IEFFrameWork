@extends('layouts.app')

@section('title', 'Teşekkürler')

@section('content')
<section class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-6 py-16">
    <div class="max-w-md w-full text-center animate__animated animate__fadeInUp">
        <div class="relative mx-auto w-20 h-20 mb-6">
            <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
            <div class="relative w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-check text-3xl"></i>
            </div>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Mesajın bize ulaştı.</h1>
        <p class="text-slate-600 mb-8 leading-relaxed">En kısa sürede dönüş yapacağız. Bu arada, blog ve GitHub repo'sunu incelemek ister misin?</p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <i class="fa-solid fa-house"></i> Anasayfa
            </a>
            <a href="/blog" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-lg transition">
                <i class="fa-solid fa-newspaper"></i> Blog
            </a>
        </div>
    </div>
</section>
@endsection
