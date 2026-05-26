@extends('layouts.admin')

@section('title', 'Dashboard')
@section('crumb', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight">Hoş geldin, {{ $authUser['name'] ?? 'Kullanıcı' }} <span class="ml-1">👋</span></h1>
    <p class="text-sm text-slate-500 mt-1">Yönetim panelinde her şey yerinde. Aşağıdaki modüllerden devam et.</p>
</div>

@php $modules = [
    ['title'=>'Mesajlar','desc'=>'İletişim formu mesajları.','href'=>'/admin/messages','icon'=>'fa-envelope','color'=>'blue'],
    ['title'=>'Randevular','desc'=>'Randevu talepleri ve takvim.','href'=>'/admin/appointments','icon'=>'fa-calendar-check','color'=>'emerald'],
    ['title'=>'Blog','desc'=>'Yazılar, kategoriler, taslaklar.','href'=>'/admin/blog','icon'=>'fa-newspaper','color'=>'violet'],
    ['title'=>'Medya','desc'=>'Görsel ve dosya yönetimi.','href'=>'/admin/media','icon'=>'fa-photo-film','color'=>'pink'],
    ['title'=>'Site Editör','desc'=>'Sayfa içeriklerini düzenle.','href'=>'/admin/editor','icon'=>'fa-pen-ruler','color'=>'indigo'],
    ['title'=>'Trafik','desc'=>'Ziyaretçi ve sayfa analizi.','href'=>'/admin/analytics','icon'=>'fa-chart-line','color'=>'green'],
    ['title'=>'Kullanıcılar','desc'=>'Üye listesi, roller, yetkiler.','href'=>'/admin/users','icon'=>'fa-users','color'=>'amber'],
    ['title'=>'Ayarlar','desc'=>'Site ayarları, mail, SEO.','href'=>'/admin/settings','icon'=>'fa-gear','color'=>'slate'],
    ['title'=>'Loglar','desc'=>'storage/logs günlük log dosyaları.','href'=>'/admin/logs','icon'=>'fa-file-lines','color'=>'rose'],
]; @endphp

<div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach($modules as $mod)
        <a href="{{ $mod['href'] }}" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-900 hover:shadow-card hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-{{ $mod['color'] }}-50 text-{{ $mod['color'] }}-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid {{ $mod['icon'] }} text-[15px]"></i>
            </div>
            <div class="flex items-start justify-between gap-2">
                <h3 class="text-[14.5px] font-bold tracking-tight">{{ $mod['title'] }}</h3>
                <span class="text-[9.5px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-green-100 text-green-700">Aktif</span>
            </div>
            <p class="text-[13px] text-slate-500 mt-1.5 leading-relaxed">{{ $mod['desc'] }}</p>
        </a>
    @endforeach
</div>

<div class="mt-8 bg-gradient-to-r from-brand-50 to-blue-50 border border-brand-100 rounded-xl p-5 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="text-[13.5px] font-semibold text-slate-900">Sistem · v{{ config('app.version','2.0.0') }}</div>
        <div class="text-[12.5px] text-slate-600 mt-0.5">Ortam: <code class="bg-white px-1.5 py-0.5 rounded text-brand-700">{{ config('app.env') }}</code> · DB: <code class="bg-white px-1.5 py-0.5 rounded text-brand-700">{{ config('database.driver') }}</code> · PHP {{ PHP_VERSION }}</div>
    </div>
    <a href="/" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">
        <i class="fa-solid fa-arrow-up-right-from-square text-[12px]"></i> Siteyi Aç
    </a>
</div>
@endsection
