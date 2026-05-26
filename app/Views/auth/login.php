@extends('layouts.app')

@section('title', 'Giriş')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-7">
            <span class="inline-flex w-12 h-12 rounded-xl bg-gradient-to-br from-brand-900 via-brand-600 to-accent-500 text-white font-extrabold text-xl items-center justify-center shadow-glow mb-4">i</span>
            <h1 class="text-2xl font-extrabold tracking-tight">Yönetime giriş</h1>
            <p class="text-sm text-slate-500 mt-1">Devam etmek için hesabınla giriş yap.</p>
        </div>

        <form method="post" action="/login" class="bg-white border border-slate-200 rounded-xl p-7 shadow-soft animate__animated animate__fadeInUp">
            @csrf
            <label class="block mb-4">
                <span class="block text-[12px] font-semibold text-slate-700 mb-1.5">E-posta</span>
                <input type="email" name="email" required autofocus value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </label>
            <label class="block mb-5">
                <span class="block text-[12px] font-semibold text-slate-700 mb-1.5">Şifre</span>
                <input type="password" name="password" required
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </label>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm hover:shadow-glow transition-all">
                Giriş Yap <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-5">
            Şifreni mi unuttun? <a href="/sifre-sifirla" class="font-semibold text-brand-700 hover:text-brand-900">Sıfırla</a>
        </p>
    </div>
</div>
@endsection
