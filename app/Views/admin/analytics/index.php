@extends('layouts.admin')

@section('title', 'Trafik · Dashboard')
@section('crumb', 'Trafik')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Trafik Analizi</h1>
        <p class="text-sm text-slate-500 mt-1">Ziyaretçi, sayfa görüntüleme ve etkileşim verileri.</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="inline-flex bg-slate-100 rounded-md p-0.5">
            @foreach([7,14,30,60] as $d)
                <a href="?days={{ $d }}" class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $days === $d ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">{{ $d }} gün</a>
            @endforeach
        </div>
        <a href="/admin/analytics/requests" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition"><i class="fa-solid fa-list text-[11px]"></i> İstekler</a>
        <a href="/admin/analytics/sessions" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition"><i class="fa-solid fa-users text-[11px]"></i> Ziyaretçiler</a>
        <a href="/admin/analytics/events" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition"><i class="fa-solid fa-bullseye text-[11px]"></i> Olaylar</a>
    </div>
</div>

{{-- Özet kartları --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
    @php $cards = [
        ['Canlı',     $summary['live_count'],     'son 15 dakika',   'fa-signal',         'green'],
        ['Bugün',     number_format($summary['today_hits']),'görüntülenme', 'fa-eye', 'blue'],
        ['Bu hafta',  number_format($summary['week_hits']), 'son 7 gün',    'fa-calendar-week','violet'],
        ['İnsan',     number_format($summary['human_sessions']),'ziyaretçi','fa-user-check','sky'],
        ['Bot',       number_format($summary['bot_sessions']),'ziyaretçi',  'fa-robot',     'amber'],
    ]; @endphp
    @foreach($cards as $c)
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-soft hover:shadow-card transition">
            <div class="flex items-start justify-between mb-2">
                <div class="text-[10.5px] uppercase tracking-widest font-bold text-slate-500">{{ $c[0] }}</div>
                <div class="w-7 h-7 rounded-md bg-{{ $c[4] }}-50 text-{{ $c[4] }}-600 flex items-center justify-center"><i class="fa-solid {{ $c[3] }} text-[11px]"></i></div>
            </div>
            <div class="text-2xl font-extrabold tracking-tight tabular-nums">{{ $c[1] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $c[2] }}</div>
        </div>
    @endforeach
</div>

{{-- Trafik grafiği --}}
<div class="bg-white border border-slate-200 rounded-xl p-5 shadow-soft mb-5">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-base font-bold">Günlük Trafik</h3>
            <p class="text-xs text-slate-500 mt-0.5">Son {{ $days }} gün — yalnızca insan ziyaretçileri</p>
        </div>
    </div>
    <div id="trendChart"></div>
</div>

{{-- Cihaz / Tarayıcı / Referer --}}
<div class="grid lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">Cihaz</h3>
            <i class="fa-solid fa-mobile-screen text-blue-500 text-[13px]"></i>
        </div>
        <div id="deviceChart" class="p-3"></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">Tarayıcı</h3>
            <i class="fa-solid fa-window-maximize text-violet-500 text-[13px]"></i>
        </div>
        <div id="browserChart" class="p-3"></div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">Kaynak</h3>
            <i class="fa-solid fa-share-from-square text-pink-500 text-[13px]"></i>
        </div>
        <div class="text-sm">
            @if(!$sources)
                <div class="px-5 py-6 text-slate-400 text-center text-sm">Veri yok</div>
            @else
                @foreach($sources as $s)
                    <div class="flex items-center justify-between px-5 py-2.5 border-t border-slate-100 first:border-t-0">
                        <span class="text-[13px] truncate {{ $s['src']==='direct'?'text-slate-500 italic':'text-slate-900' }}">{{ $s['src'] }}</span>
                        <span class="text-[13px] font-semibold tabular-nums">{{ $s['c'] }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- En çok ziyaret edilenler + Canlı --}}
<div class="grid lg:grid-cols-2 gap-5 mb-5">
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">En Çok Ziyaret Edilen</h3>
            <i class="fa-solid fa-fire text-amber-500 text-[13px]"></i>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-white border-b border-slate-100">
                <tr>
                    <th class="text-left px-5 py-2 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Sayfa</th>
                    <th class="text-right px-5 py-2 text-[11px] uppercase tracking-wider text-slate-400 font-bold w-20">Hits</th>
                    <th class="text-right px-5 py-2 text-[11px] uppercase tracking-wider text-slate-400 font-bold w-20">Uniq</th>
                </tr>
            </thead>
            <tbody>
                @if(!$topPaths)
                    <tr><td colspan="3" class="text-center text-slate-400 py-6">Veri yok</td></tr>
                @endif
                @foreach($topPaths as $p)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-[12.5px] text-brand-700 truncate max-w-xs" title="{{ $p['path'] }}">{{ str_limit($p['path'], 38) }}</td>
                        <td class="text-right px-5 py-3 font-semibold tabular-nums">{{ number_format((int)$p['hits']) }}</td>
                        <td class="text-right px-5 py-3 text-slate-500 tabular-nums">{{ number_format((int)$p['uniques']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 flex items-center gap-2">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                Canlı Ziyaretçiler ({{ $summary['live_count'] }})
            </h3>
            <a href="/admin/analytics/sessions" class="text-[11px] text-brand-700 hover:text-brand-900 font-semibold">Tümü →</a>
        </div>
        <div>
            @if(!$live)
                <div class="px-5 py-10 text-center text-slate-400 text-sm">Şu an aktif ziyaretçi yok.</div>
            @endif
            @foreach($live as $l)
                <a href="/admin/analytics/sessions/{{ $l['id'] }}" class="flex items-center gap-3 px-5 py-2.5 border-t border-slate-100 first:border-t-0 hover:bg-slate-50 transition">
                    <i class="fa-solid {{ $l['device']==='mobile'?'fa-mobile-screen':($l['device']==='tablet'?'fa-tablet-screen-button':'fa-desktop') }} text-slate-400 text-[13px] w-4 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-900 truncate">{{ $l['browser'] }} · {{ $l['os'] }}</div>
                        <div class="text-[11.5px] text-slate-500 truncate">{{ $l['ip'] }} · son: <code class="font-mono">{{ str_limit($l['last_path'] ?? '?', 28) }}</code></div>
                    </div>
                    <span class="text-[11px] text-slate-400">{{ format_datetime($l['last_seen_at'], 'H:i') }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const trendData = {!! json_encode(array_values($trend)) !!};
const devices  = {!! json_encode(array_values($devices)) !!};
const browsers = {!! json_encode(array_values($browsers)) !!};

new ApexCharts(document.getElementById('trendChart'), {
    chart: { type:'area', height:280, toolbar:{show:false}, zoom:{enabled:false}, fontFamily:'Poppins,sans-serif',
             animations:{enabled:true,easing:'easeinout',speed:600} },
    series: [
        { name:'Görüntülenme', data: trendData.map(d => d.total) },
        { name:'Benzersiz',    data: trendData.map(d => d.uniq) },
    ],
    colors:['#1e40af','#06b6d4'],
    stroke:{ curve:'smooth', width:2.5 },
    fill:{ type:'gradient', gradient:{shadeIntensity:1,opacityFrom:.3,opacityTo:.02,stops:[0,90,100]} },
    grid:{ borderColor:'#e2e8f0', strokeDashArray:3 },
    xaxis:{
        categories: trendData.map(d => new Date(d.day).toLocaleDateString('tr-TR',{day:'2-digit',month:'short'})),
        axisBorder:{show:false}, axisTicks:{show:false},
        labels:{ style:{colors:'#94a3b8',fontSize:'11px'} }
    },
    yaxis:{ labels:{ style:{colors:'#94a3b8',fontSize:'11px'} } },
    legend:{ position:'top', horizontalAlign:'right', fontSize:'12px', markers:{width:10,height:10} },
    dataLabels:{ enabled:false },
    tooltip:{ theme:'light', y:{ formatter: v => v.toLocaleString('tr-TR') } },
}).render();

if (devices.length) {
    new ApexCharts(document.getElementById('deviceChart'), {
        chart:{ type:'donut', height:200, fontFamily:'Poppins,sans-serif' },
        series: devices.map(d => parseInt(d.c)),
        labels: devices.map(d => d.k || '?'),
        colors:['#1e40af','#06b6d4','#a855f7'],
        legend:{ position:'bottom', fontSize:'12px' },
        plotOptions:{ pie:{ donut:{size:'62%'} } },
        dataLabels:{ enabled:false },
    }).render();
} else { document.getElementById('deviceChart').innerHTML = '<div class="text-center text-slate-400 py-8 text-sm">Veri yok</div>'; }

if (browsers.length) {
    new ApexCharts(document.getElementById('browserChart'), {
        chart:{ type:'bar', height:200, toolbar:{show:false}, fontFamily:'Poppins,sans-serif' },
        series: [{ name:'Ziyaret', data: browsers.map(d => parseInt(d.c)) }],
        xaxis:{ categories: browsers.map(d => d.k || '?'),
                axisBorder:{show:false}, axisTicks:{show:false},
                labels:{ style:{colors:'#94a3b8',fontSize:'11px'} } },
        yaxis:{ labels:{ style:{colors:'#94a3b8',fontSize:'11px'} } },
        grid:{ borderColor:'#e2e8f0', strokeDashArray:3 },
        colors:['#1e40af'],
        plotOptions:{ bar:{ borderRadius:4, columnWidth:'55%' } },
        dataLabels:{ enabled:false },
    }).render();
} else { document.getElementById('browserChart').innerHTML = '<div class="text-center text-slate-400 py-8 text-sm">Veri yok</div>'; }
</script>
@endsection
