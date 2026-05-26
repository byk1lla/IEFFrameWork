@extends('layouts.admin')

@section('title', 'Trafik · İstek Logu')
@section('crumb', 'Trafik · İstekler')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">İstek Logu</h1>
        <p class="text-sm text-slate-500 mt-1">{{ number_format($total) }} kayıt · sayfa {{ $page }}/{{ max(1,$totalPages) }}</p>
    </div>
    <a href="/admin/analytics" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i> Dashboard
    </a>
</div>

{{-- Filtre formu --}}
<form method="get" action="/admin/analytics/requests" class="bg-white border border-slate-200 rounded-xl p-4 mb-5 shadow-soft grid md:grid-cols-7 gap-2.5">
    <input type="text"   name="path"   placeholder="path"   value="{{ $filters['path'] }}"   class="md:col-span-2 px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <input type="text"   name="ip"     placeholder="IP"     value="{{ $filters['ip'] }}"     class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <input type="number" name="status" placeholder="200"    value="{{ $filters['status'] }}" class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <select name="method" class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
        <option value="">— method —</option>
        @foreach(['GET','POST','PUT','PATCH','DELETE'] as $m)<option value="{{ $m }}" {{ $filters['method']===$m?'selected':'' }}>{{ $m }}</option>@endforeach
    </select>
    <select name="bot" class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
        <option value="all"   {{ $filters['bot']==='all'?'selected':'' }}>Tümü</option>
        <option value="human" {{ $filters['bot']==='human'?'selected':'' }}>İnsan</option>
        <option value="bot"   {{ $filters['bot']==='bot'?'selected':'' }}>Bot</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md"><i class="fa-solid fa-filter text-[11px]"></i> Filtre</button>
</form>

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-24">Zaman</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-16">Method</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold">Path</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-16">St.</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-16">ms</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-32">IP / Cihaz</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-12">Bot</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-20">Ziyaretçi</th>
            </tr>
        </thead>
        <tbody>
            @if(!$rows)
                <tr><td colspan="8" class="text-center py-10 text-slate-400">Bu filtreyle eşleşen kayıt yok.</td></tr>
            @endif
            @foreach($rows as $r)
                @php
                    $st = (int) $r['status_code'];
                    $stColor = $st < 300 ? 'pill-success' : ($st < 400 ? 'pill-brand' : ($st < 500 ? 'pill-warning' : 'pill-danger'));
                    $stBg = $st < 300 ? 'bg-green-100 text-green-700' : ($st < 400 ? 'bg-brand-50 text-brand-700' : ($st < 500 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'));
                @endphp
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-2.5 text-[11.5px] text-slate-500 font-mono">{{ date('d.m H:i:s', strtotime($r['occurred_at'])) }}</td>
                    <td class="px-4 py-2.5"><span class="text-[10.5px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">{{ $r['method'] }}</span></td>
                    <td class="px-4 py-2.5"><code class="text-[12px] text-brand-800 font-mono">{{ str_limit($r['path'], 60) }}</code></td>
                    <td class="px-4 py-2.5"><span class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded-full {{ $stBg }}">{{ $st }}</span></td>
                    <td class="px-4 py-2.5 text-[12px] text-slate-500 tabular-nums">{{ $r['response_ms'] }}</td>
                    <td class="px-4 py-2.5">
                        <div class="text-[12px] font-mono text-slate-700">{{ $r['ip'] ?? '—' }}</div>
                        <div class="text-[10.5px] text-slate-400">{{ $r['browser'] ?? '?' }} · {{ $r['device'] ?? '?' }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        @if($r['is_bot'])<i class="fa-solid fa-robot text-amber-500 text-[12px]" title="Bot"></i>@else<i class="fa-solid fa-user text-green-500 text-[12px]" title="İnsan"></i>@endif
                    </td>
                    <td class="px-4 py-2.5"><a href="/admin/analytics/sessions/{{ $r['session_id'] }}" class="text-[12px] text-brand-700 hover:text-brand-900 font-semibold">#{{ $r['session_id'] }} →</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Sayfalama --}}
    @if($totalPages > 1)
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-200 bg-slate-50">
            <div class="text-[12px] text-slate-500">Sayfa {{ $page }}/{{ $totalPages }}</div>
            <div class="flex gap-1">
                @php $base = '/admin/analytics/requests?' . http_build_query(array_filter($filters)); @endphp
                @if($page > 1)
                    <a href="{{ $base }}&page={{ $page-1 }}" class="px-3 py-1.5 text-[12px] font-semibold bg-white border border-slate-200 rounded hover:bg-slate-100">‹ Önceki</a>
                @endif
                @if($page < $totalPages)
                    <a href="{{ $base }}&page={{ $page+1 }}" class="px-3 py-1.5 text-[12px] font-semibold bg-white border border-slate-200 rounded hover:bg-slate-100">Sonraki ›</a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
