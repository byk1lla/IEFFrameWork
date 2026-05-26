@extends('layouts.admin')

@section('title', 'Ziyaretçi #' . $session['id'])
@section('crumb', 'Trafik · Ziyaretçi #' . $session['id'])

@section('content')
<a href="/admin/analytics/sessions" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Ziyaretçilere dön
</a>

{{-- Session meta --}}
<div class="grid lg:grid-cols-3 gap-4 mb-5">
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-5 shadow-soft">
        <div class="flex items-center gap-3 mb-4">
            @php $devIcon = match($session['device'] ?? 'desktop') {
                'mobile' => 'fa-mobile-screen','tablet' => 'fa-tablet-screen-button', default => 'fa-desktop',
            }; @endphp
            <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center">
                <i class="fa-solid {{ $devIcon }} text-[15px]"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold tracking-tight">{{ $session['browser'] }} · {{ $session['os'] }}</h2>
                    @if($session['is_bot'])
                        <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-amber-100 text-amber-700">Bot</span>
                    @else
                        <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-green-100 text-green-700">İnsan</span>
                    @endif
                </div>
                <div class="text-sm text-slate-500 mt-0.5">{{ $session['device'] }} · fingerprint <code class="font-mono text-xs text-slate-400">{{ substr($session['fingerprint'], 0, 16) }}…</code></div>
            </div>
        </div>
        <dl class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-3 text-[13px]">
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">IP</dt><dd class="font-mono text-slate-900 mt-0.5">{{ $session['ip'] ?? '—' }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">İlk ziyaret</dt><dd class="mt-0.5">{{ format_datetime($session['first_seen_at']) }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Son ziyaret</dt><dd class="mt-0.5">{{ format_datetime($session['last_seen_at']) }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">İstek sayısı</dt><dd class="mt-0.5 tabular-nums font-semibold">{{ $session['request_count'] }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Kaynak</dt><dd class="mt-0.5">{{ $session['referrer_host'] ?: 'direct' }}</dd></div>
            @if($session['utm_source'])
                <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">UTM</dt><dd class="mt-0.5 text-xs">{{ $session['utm_source'] }}/{{ $session['utm_medium'] }}</dd></div>
            @endif
        </dl>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <div class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold mb-1">User Agent</div>
            <code class="text-[11.5px] font-mono text-slate-600 break-all">{{ $session['user_agent'] }}</code>
        </div>
    </div>

    {{-- En çok ziyaret edilen sayfalar (bu ziyaretçi içinde) --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">En Çok Gezdi</h3>
        </div>
        @if(!$pages)
            <div class="py-6 text-center text-slate-400 text-sm">Henüz veri yok</div>
        @endif
        @foreach($pages as $p)
            <div class="flex items-center justify-between px-5 py-2.5 border-t border-slate-100 first:border-t-0">
                <code class="text-[12px] text-brand-700 font-mono truncate" title="{{ $p['path'] }}">{{ str_limit($p['path'], 30) }}</code>
                <span class="text-[12px] font-semibold tabular-nums">{{ $p['c'] }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Timeline: logs + events --}}
<div class="grid lg:grid-cols-2 gap-5">
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">HTTP İstekleri ({{ count($logs) }})</h3>
        </div>
        <div class="max-h-[600px] overflow-y-auto">
            @if(!$logs)<div class="py-6 text-center text-slate-400 text-sm">Kayıt yok</div>@endif
            @foreach($logs as $l)
                <div class="px-5 py-2.5 border-t border-slate-100 first:border-t-0 flex items-center gap-3">
                    <span class="text-[10px] text-slate-400 font-mono w-14">{{ date('H:i:s', strtotime($l['occurred_at'])) }}</span>
                    <span class="text-[10.5px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 w-12 text-center">{{ $l['method'] }}</span>
                    <code class="flex-1 text-[12px] text-brand-700 font-mono truncate" title="{{ $l['path'] }}">{{ str_limit($l['path'], 50) }}</code>
                    <span class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full {{ $l['status_code']<300?'bg-green-100 text-green-700':($l['status_code']<400?'bg-brand-50 text-brand-700':'bg-red-100 text-red-700') }}">{{ $l['status_code'] }}</span>
                    <span class="text-[11px] text-slate-400 tabular-nums">{{ $l['response_ms'] }}ms</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">Etkileşim Olayları ({{ count($events) }})</h3>
        </div>
        <div class="max-h-[600px] overflow-y-auto">
            @if(!$events)<div class="py-6 text-center text-slate-400 text-sm">Olay yok</div>@endif
            @foreach($events as $e)
                <div class="px-5 py-2.5 border-t border-slate-100 first:border-t-0 flex items-start gap-3">
                    <span class="text-[10px] text-slate-400 font-mono w-14 mt-0.5">{{ date('H:i:s', strtotime($e['occurred_at'])) }}</span>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-violet-50 text-violet-700 w-24 text-center">{{ $e['event_type'] }}</span>
                    <div class="flex-1 min-w-0">
                        @if($e['target'])<code class="text-[12px] text-slate-700 font-mono truncate block" title="{{ $e['target'] }}">{{ str_limit($e['target'], 45) }}</code>@endif
                        @if($e['value'])<div class="text-[11px] text-slate-500 truncate" title="{{ $e['value'] }}">{{ str_limit($e['value'], 60) }}</div>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
