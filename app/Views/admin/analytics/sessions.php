@extends('layouts.admin')

@section('title', 'Trafik · Ziyaretçiler')
@section('crumb', 'Trafik · Ziyaretçiler')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Ziyaretçiler</h1>
        <p class="text-sm text-slate-500 mt-1">Son 200 tekil ziyaretçi</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="inline-flex bg-slate-100 rounded-md p-0.5">
            <a href="?filter=human" class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter==='human' ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">İnsan</a>
            <a href="?filter=bot"   class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter==='bot'   ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">Bot</a>
            <a href="?filter=all"   class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter==='all'   ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">Tümü</a>
        </div>
        <a href="/admin/analytics" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i> Dashboard
        </a>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
    <table id="sessionsTable" class="display w-full">
        <thead>
            <tr>
                <th style="width:48px">#</th>
                <th>IP / Tarayıcı</th>
                <th style="width:100px">Cihaz / OS</th>
                <th style="width:80px">İstek</th>
                <th>Son Path</th>
                <th style="width:140px">Son aktivite</th>
                <th style="width:80px">Tür</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $s)
                @php
                    $devIcon = match($s['device'] ?? 'desktop') {
                        'mobile' => 'fa-mobile-screen',
                        'tablet' => 'fa-tablet-screen-button',
                        default  => 'fa-desktop',
                    };
                @endphp
                <tr>
                    <td class="text-slate-400 font-mono text-xs">{{ $s['id'] }}</td>
                    <td>
                        <a href="/admin/analytics/sessions/{{ $s['id'] }}" class="font-mono text-[13px] font-semibold text-brand-700 hover:text-brand-900">{{ $s['ip'] ?? '—' }}</a>
                        <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs">{{ $s['browser'] ?? '?' }} · {{ $s['referrer_host'] ?: 'direct' }}</div>
                    </td>
                    <td>
                        <i class="fa-solid {{ $devIcon }} text-slate-400 text-[11px] mr-1"></i>
                        <span class="text-[12px] text-slate-700">{{ $s['device'] }}</span>
                        <div class="text-[10.5px] text-slate-400">{{ $s['os'] }}</div>
                    </td>
                    <td><span class="text-[12px] font-semibold tabular-nums">{{ $s['request_count'] }}</span></td>
                    <td><code class="text-[11.5px] text-brand-700 font-mono">{{ str_limit($s['last_path'] ?? '?', 40) }}</code></td>
                    <td class="text-[12px] text-slate-500">{{ format_datetime($s['last_seen_at']) }}</td>
                    <td>
                        @if($s['is_bot'])
                            <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-amber-100 text-amber-700">Bot</span>
                        @else
                            <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-green-100 text-green-700">İnsan</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>$(function(){ $('#sessionsTable').DataTable({ order:[[5,'desc']], pageLength:25 }); });</script>
@endsection
