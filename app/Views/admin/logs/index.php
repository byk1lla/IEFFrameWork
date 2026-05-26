@extends('layouts.admin')

@section('title', 'Loglar')
@section('crumb', 'Loglar')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Loglar</h1>
    <p class="text-sm text-slate-500 mt-1">storage/logs altındaki günlük dosyalar.</p>
</div>

@if(!$files)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-file-lines text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Henüz log dosyası yok.</p>
    </div>
@else
    <div class="grid lg:grid-cols-[260px_1fr] gap-4">
        <aside class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-widest font-bold text-slate-500">
                Dosyalar ({{ count($files) }})
            </div>
            <ul class="max-h-[540px] overflow-y-auto divide-y divide-slate-100">
                @foreach($files as $f)
                    <li>
                        <a href="/admin/logs?file={{ $f }}" class="block px-4 py-2.5 text-[13px] font-mono transition {{ $current === $f ? 'bg-brand-50 text-brand-800 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="fa-solid {{ str_contains($f,'error')?'fa-triangle-exclamation text-red-500':'fa-file-lines text-slate-400' }} text-[12px] mr-1.5"></i>
                            {{ $f }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>
        <div class="bg-slate-950 border border-slate-200 rounded-xl overflow-hidden min-h-[400px]">
            @if($current === '')
                <div class="py-20 text-center text-slate-500">
                    <i class="fa-solid fa-arrow-left text-2xl mb-2"></i>
                    <p>Soldan bir log dosyası seç.</p>
                </div>
            @else
                <div class="flex items-center justify-between px-5 py-3 bg-white/5 border-b border-white/10 text-slate-400 text-xs font-mono">
                    <span>{{ $current }}</span>
                    <span>{{ number_format($size) }} byte</span>
                </div>
                <pre class="px-5 py-4 text-[12px] text-slate-200 font-mono leading-relaxed overflow-auto max-h-[640px] whitespace-pre-wrap break-words">{{ $content }}</pre>
            @endif
        </div>
    </div>
@endif
@endsection
