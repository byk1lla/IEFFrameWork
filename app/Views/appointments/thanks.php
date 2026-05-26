@extends('layouts.app')

@section('title', 'Randevu Alındı')

@section('content')
<section class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-6 py-16">
    <div class="max-w-md w-full text-center animate__animated animate__fadeInUp">
        <div class="relative mx-auto w-20 h-20 mb-6">
            <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
            <div class="relative w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-calendar-check text-3xl"></i>
            </div>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Randevu talebin alındı!</h1>
        @if($appt)
            <div class="bg-white border border-slate-200 rounded-xl p-5 mb-6 text-left">
                <div class="text-[11px] uppercase tracking-widest text-slate-400 font-bold mb-2">Detaylar</div>
                <ul class="text-sm space-y-1.5">
                    <li><strong class="text-slate-500 font-semibold">Talep no:</strong> #{{ $appt->id }}</li>
                    <li><strong class="text-slate-500 font-semibold">Tarih:</strong> {{ format_datetime($appt->start_at) }}</li>
                    <li><strong class="text-slate-500 font-semibold">Ad:</strong> {{ $appt->customer_name }}</li>
                    <li><strong class="text-slate-500 font-semibold">E-posta:</strong> {{ $appt->email }}</li>
                </ul>
            </div>
        @endif
        <p class="text-slate-600 mb-6 leading-relaxed">Talebin onaylandıktan sonra e-posta ile sana dönüş yapacağız.</p>
        <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <i class="fa-solid fa-house"></i> Anasayfaya Dön
        </a>
    </div>
</section>
@endsection
