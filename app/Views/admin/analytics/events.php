@extends('layouts.admin')

@section('title', 'Trafik · Olaylar')
@section('crumb', 'Trafik · Olaylar')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Etkileşim Olayları</h1>
        <p class="text-sm text-slate-500 mt-1">{{ number_format($total) }} olay · click, scroll, form_submit vb.</p>
    </div>
    <a href="/admin/analytics" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i> Dashboard
    </a>
</div>

<form method="get" action="/admin/analytics/events" class="bg-white border border-slate-200 rounded-xl p-4 mb-5 shadow-soft grid md:grid-cols-5 gap-2.5">
    <select name="type" class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
        <option value="">— olay tipi —</option>
        @foreach($types as $t)<option value="{{ $t['event_type'] }}" {{ $filters['type']===$t['event_type']?'selected':'' }}>{{ $t['event_type'] }} ({{ $t['c'] }})</option>@endforeach
    </select>
    <input type="text" name="path"   placeholder="path"   value="{{ $filters['path'] }}"   class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <input type="text" name="target" placeholder="target" value="{{ $filters['target'] }}" class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <input type="text" name="ip"     placeholder="IP"     value="{{ $filters['ip'] }}"     class="px-3 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10">
    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md"><i class="fa-solid fa-filter text-[11px]"></i> Filtre</button>
</form>

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-32">Zaman</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-28">Tip</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold">Path / Target</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-40">Değer</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-24">IP</th>
                <th class="text-left px-4 py-2.5 text-[10.5px] uppercase tracking-wider text-slate-400 font-bold w-20">Ziyaretçi</th>
            </tr>
        </thead>
        <tbody>
            @if(!$rows)
                <tr><td colspan="6" class="text-center py-10 text-slate-400">Kayıt yok.</td></tr>
            @endif
            @foreach($rows as $r)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-2.5 text-[11.5px] text-slate-500 font-mono">{{ date('d.m H:i:s', strtotime($r['occurred_at'])) }}</td>
                    <td class="px-4 py-2.5">
                        @php $typeColor = match($r['event_type']) {
                            'page_view' => 'bg-blue-50 text-blue-700',
                            'click'     => 'bg-violet-50 text-violet-700',
                            'scroll'    => 'bg-slate-100 text-slate-700',
                            'form_submit','form_focus' => 'bg-green-50 text-green-700',
                            default     => 'bg-amber-50 text-amber-700',
                        }; @endphp
                        <span class="text-[10.5px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $typeColor }}">{{ $r['event_type'] }}</span>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="font-mono text-[12px] text-brand-700 truncate" title="{{ $r['path'] }}">{{ str_limit($r['path'] ?? '', 50) }}</div>
                        @if($r['target'])<div class="text-[11px] text-slate-500 truncate" title="{{ $r['target'] }}">{{ str_limit($r['target'], 60) }}</div>@endif
                    </td>
                    <td class="px-4 py-2.5 text-[12px] text-slate-600 truncate" title="{{ $r['value'] }}">{{ str_limit($r['value'] ?? '', 30) }}</td>
                    <td class="px-4 py-2.5 text-[11.5px] font-mono text-slate-500">{{ $r['ip'] ?? '—' }}</td>
                    <td class="px-4 py-2.5"><a href="/admin/analytics/sessions/{{ $r['session_id'] }}" class="text-[12px] text-brand-700 hover:text-brand-900 font-semibold">#{{ $r['session_id'] }} →</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($totalPages > 1)
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-200 bg-slate-50">
            <div class="text-[12px] text-slate-500">Sayfa {{ $page }}/{{ $totalPages }}</div>
            <div class="flex gap-1">
                @php $base = '/admin/analytics/events?' . http_build_query(array_filter($filters)); @endphp
                @if($page > 1)<a href="{{ $base }}&page={{ $page-1 }}" class="px-3 py-1.5 text-[12px] font-semibold bg-white border border-slate-200 rounded hover:bg-slate-100">‹ Önceki</a>@endif
                @if($page < $totalPages)<a href="{{ $base }}&page={{ $page+1 }}" class="px-3 py-1.5 text-[12px] font-semibold bg-white border border-slate-200 rounded hover:bg-slate-100">Sonraki ›</a>@endif
            </div>
        </div>
    @endif
</div>
@endsection
