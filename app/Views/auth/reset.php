@extends('layouts.app')

@section('title', 'Yeni Şifre Belirle')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-7">
            <span class="inline-flex w-12 h-12 rounded-xl bg-gradient-to-br from-brand-900 via-brand-600 to-accent-500 text-white items-center justify-center shadow-glow mb-4">
                <i class="fa-solid fa-lock text-lg"></i>
            </span>
            <h1 class="text-2xl font-extrabold tracking-tight">Yeni şifre belirle</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $email }} hesabı için</p>
        </div>

        <form method="post" action="/sifre-sifirla/{{ $token }}" class="bg-white border border-slate-200 rounded-xl p-7 shadow-soft animate__animated animate__fadeInUp">
            @csrf
            <label class="block mb-4">
                <span class="block text-[12px] font-semibold text-slate-700 mb-1.5">Yeni Şifre</span>
                <input type="password" name="password" required minlength="6" autofocus autocomplete="new-password"
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </label>
            <label class="block mb-5">
                <span class="block text-[12px] font-semibold text-slate-700 mb-1.5">Şifre (tekrar)</span>
                <input type="password" name="password_confirmation" required minlength="6" autocomplete="new-password"
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </label>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition-all">
                <i class="fa-solid fa-check text-[12px]"></i> Şifreyi güncelle
            </button>
        </form>
    </div>
</div>
@endsection
