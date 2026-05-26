@extends('layouts.admin')

@section('title', 'Site Editör')
@section('crumb', 'Site Editör')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Sayfa Editörü</h1>
        <p class="text-sm text-slate-500 mt-1">Düzenlemek istediğin sayfayı seç — yeni sekmede inline editör açılır, metne/görsele/ikona tıkla yaz.</p>
    </div>
    <a href="/admin/editor/blocks" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-700 hover:text-slate-900 text-sm font-semibold rounded-md transition">
        <i class="fa-solid fa-list"></i> Manuel Bloklar
    </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($pages as $p)
        @php
            $count = $counts[$p['page']] ?? 0;
            $color = $p['color'];
        @endphp
        <a href="{{ $p['url'] }}?edit=1" target="_blank"
           class="group block bg-white border border-slate-200 hover:border-{{ $color }}-500 rounded-xl p-6 transition-all hover:shadow-card hover:-translate-y-1">

            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $p['icon'] }} text-xl"></i>
                </div>
                @if($count > 0)
                    <span class="text-[10.5px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700">
                        {{ $count }} düzenleme
                    </span>
                @else
                    <span class="text-[10.5px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                        Default
                    </span>
                @endif
            </div>

            <h3 class="text-lg font-bold tracking-tight mb-1 text-slate-900 group-hover:text-{{ $color }}-700 transition">{{ $p['title'] }}</h3>
            <p class="text-[13px] text-slate-500 mb-4 leading-relaxed">{{ $p['desc'] }}</p>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <code class="text-[11.5px] font-mono text-slate-400">{{ $p['url'] }}</code>
                <span class="inline-flex items-center gap-1 text-[12.5px] font-semibold text-{{ $color }}-700 group-hover:gap-2 transition-all">
                    Düzenle <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </span>
            </div>
        </a>
    @endforeach
</div>

<div class="mt-8 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-5">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-base font-bold text-slate-900 mb-1.5">Nasıl çalışır?</h4>
            <ol class="text-[13px] text-slate-700 space-y-1 list-decimal pl-5">
                <li>Yukarıdaki sayfa kartlarından birine tıkla — yeni sekmede sayfa <code class="bg-white px-1.5 py-0.5 rounded text-brand-700 font-mono">?edit=1</code> ile açılır.</li>
                <li>Üstte <strong class="text-amber-700">turuncu sticky bar</strong> görünür: "Düzenle / Gezinme Moduna Geç".</li>
                <li>Metne tıkla → yaz → tab/blur ile <strong>otomatik kaydolur</strong> (toast bildirim).</li>
                <li>Görsele tıkla → modal açılır → dosya seç/URL gir.</li>
                <li>İkona tıkla → <strong>400+ Font Awesome</strong> ikon galerisi açılır.</li>
                <li>İşin bitince <strong class="text-slate-900">Kapat</strong> ile editör'den çık.</li>
            </ol>
        </div>
    </div>
</div>

@endsection
