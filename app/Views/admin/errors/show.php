@extends('layouts.admin')

@section('title', 'Hata #' . $row['id'])
@section('crumb', 'Hata Raporları · #' . $row['id'])

@section('content')
<div class="mb-6 flex items-start justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="/admin/error-reports" class="text-slate-500 hover:text-slate-900 text-sm"><i class="fa-solid fa-arrow-left"></i> Geri</a>
            @if($row['is_fixed'])
                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-green-100 text-green-700"><i class="fa-solid fa-check"></i> Çözüldü</span>
            @else
                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-red-100 text-red-700"><i class="fa-solid fa-circle text-[8px]"></i> Açık</span>
            @endif
            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-slate-100 text-slate-700">{{ $row['occurrence_count'] }}x görüldü</span>
        </div>
        <h1 class="text-xl font-extrabold tracking-tight font-mono break-all">{{ $row['exception'] }}</h1>
        <p class="text-sm text-slate-600 mt-2">{{ $row['message'] }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if(!$row['is_fixed'])
            <form method="POST" action="/admin/error-reports/{{ $row['id'] }}/fix" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md transition">
                    <i class="fa-solid fa-check"></i> Çözüldü
                </button>
            </form>
        @endif
        <form method="POST" action="/admin/error-reports/{{ $row['id'] }}/delete" class="inline error-del">
            @csrf
            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-red-400 hover:text-red-700 text-slate-700 text-sm font-semibold rounded-md transition">
                <i class="fa-solid fa-trash"></i> Sil
            </button>
        </form>
    </div>
</div>

{{-- User Note --}}
@if(!empty($row['user_note']))
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-5">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-amber-700 mb-2"><i class="fa-solid fa-quote-left mr-1"></i> Kullanıcı Notu</div>
        <p class="text-[14px] text-amber-900">{{ $row['user_note'] }}</p>
    </div>
@endif

{{-- Meta grid --}}
<div class="grid md:grid-cols-2 gap-3 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-2">Dosya / Satır</div>
        <code class="text-[12.5px] font-mono text-slate-900 break-all">{{ $row['file'] }}<span class="text-red-600">:{{ $row['line'] }}</span></code>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-2">Hash</div>
        <code class="text-[11.5px] font-mono text-slate-700 break-all">{{ $row['hash'] }}</code>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-2">İlk Görülme</div>
        <div class="text-[13px]">{{ format_date($row['first_seen_at']) }}</div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-2">Son Görülme</div>
        <div class="text-[13px]">{{ format_date($row['last_seen_at']) }}</div>
    </div>
    @if($row['fixed_at'])
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="text-[10.5px] font-bold uppercase tracking-widest text-green-700 mb-2">Çözüldü</div>
            <div class="text-[13px] text-green-900">{{ format_date($row['fixed_at']) }}</div>
        </div>
    @endif
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-2">Mail Gönderildi</div>
        <div class="text-[13px]">{{ $row['mail_sent_count'] }} kez</div>
    </div>
</div>

{{-- Trace --}}
@if(!empty($row['trace_decoded']) && is_array($row['trace_decoded']))
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-5">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-list-ol mr-2 text-slate-400"></i> Stack Trace</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[12.5px]">
                @foreach(array_slice($row['trace_decoded'], 0, 30) as $i => $t)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2 text-slate-400 font-mono text-right w-12">#{{ $i }}</td>
                        <td class="px-4 py-2">
                            @if(!empty($t['file']))
                                <code class="text-slate-700 font-mono">{{ $t['file'] }}<span class="text-red-600">:{{ $t['line'] ?? '?' }}</span></code>
                            @endif
                            <div class="text-brand-700 font-mono text-[12px] mt-0.5">{{ $t['function'] ?? '' }}</div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif

{{-- Request --}}
@if(!empty($row['request_decoded']))
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-5">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-globe mr-2 text-slate-400"></i> Request</h3>
        </div>
        <pre class="p-5 bg-slate-900 text-slate-100 text-[12px] font-mono overflow-x-auto">{{ json_encode($row['request_decoded'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
@endif

{{-- Server --}}
@if(!empty($row['server_decoded']))
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-5">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-server mr-2 text-slate-400"></i> Sunucu / Ortam</h3>
        </div>
        <pre class="p-5 bg-slate-900 text-slate-100 text-[12px] font-mono overflow-x-auto">{{ json_encode($row['server_decoded'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
@endif
@endsection

@section('scripts')
<script>
document.querySelectorAll('.error-del button').forEach(btn => btn.addEventListener('click', () => {
    const form = btn.closest('form');
    Swal.fire({ title:'Bu hata raporu silinsin mi?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#dc2626', confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç' })
        .then(r => r.isConfirmed && form.submit());
}));
</script>
@endsection
