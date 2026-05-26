@extends('layouts.admin')

@section('title', 'Hata Raporları')
@section('crumb', 'Hata Raporları')

@section('content')
<div class="mb-6 flex items-end justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Hata Raporları</h1>
        <p class="text-sm text-slate-500 mt-1">Exception sayfasından gönderilen hatalar; aynı hata bir kez agrega edilir.</p>
    </div>
    @if($stats['fixed'] > 0)
        <form method="POST" action="/admin/error-reports/clear-fixed" class="clear-fixed-form">
            @csrf
            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-red-400 hover:text-red-700 text-slate-700 text-sm font-semibold rounded-md transition">
                <i class="fa-solid fa-broom"></i> Çözülmüşleri Sil ({{ $stats['fixed'] }})
            </button>
        </form>
    @endif
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-slate-500">Toplam</div>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</div>
    </div>
    <div class="bg-white border border-red-100 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-red-700">Açık</div>
        <div class="text-2xl font-extrabold text-red-700 mt-1">{{ $stats['open'] }}</div>
    </div>
    <div class="bg-white border border-green-100 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-green-700">Çözülmüş</div>
        <div class="text-2xl font-extrabold text-green-700 mt-1">{{ $stats['fixed'] }}</div>
    </div>
    <div class="bg-white border border-amber-100 rounded-xl p-4">
        <div class="text-[10.5px] font-bold uppercase tracking-widest text-amber-700">Bugün</div>
        <div class="text-2xl font-extrabold text-amber-700 mt-1">{{ $stats['today'] }}</div>
    </div>
</div>

@if(!$rows)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-circle-check text-3xl text-green-500 mb-3"></i>
        <h3 class="text-base font-bold mb-1">Hiç hata raporlanmamış</h3>
        <p class="text-sm text-slate-500">Uygulamada exception oluşursa "Hatayı Raporla" butonuyla buraya düşer.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="errorsTable" class="display w-full">
            <thead>
                <tr>
                    <th style="width:80px">Durum</th>
                    <th>Exception</th>
                    <th>Dosya:Satır</th>
                    <th style="width:80px">Sayı</th>
                    <th style="width:60px">Mail</th>
                    <th style="width:160px">Son Görülme</th>
                    <th style="width:120px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr class="{{ $r['is_fixed'] ? 'opacity-60' : '' }}">
                        <td>
                            @if($r['is_fixed'])
                                <span class="inline-flex items-center gap-1 text-[10.5px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-green-100 text-green-700"><i class="fa-solid fa-check"></i> Çözüldü</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10.5px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-red-100 text-red-700"><i class="fa-solid fa-circle"></i> Açık</span>
                            @endif
                        </td>
                        <td>
                            <a href="/admin/error-reports/{{ $r['id'] }}" class="text-brand-700 hover:text-brand-900 font-semibold">{{ $r['exception'] }}</a>
                            <div class="text-[12px] text-slate-500 truncate max-w-md">{{ str_limit($r['message'] ?: '—', 100) }}</div>
                        </td>
                        <td>
                            <code class="text-[11.5px] text-slate-600 font-mono">{{ basename($r['file']) }}<span class="text-red-600">:{{ $r['line'] }}</span></code>
                        </td>
                        <td>
                            <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[12px] font-bold">{{ $r['occurrence_count'] }}</span>
                        </td>
                        <td>
                            <span class="text-[12px] text-slate-500">{{ $r['mail_sent_count'] }}</span>
                        </td>
                        <td class="text-[12px] text-slate-500">{{ format_date($r['last_seen_at']) }}</td>
                        <td>
                            <div class="flex items-center gap-2 text-[12.5px]">
                                <a href="/admin/error-reports/{{ $r['id'] }}" class="text-brand-700 hover:text-brand-900"><i class="fa-solid fa-eye"></i></a>
                                @if(!$r['is_fixed'])
                                    <form method="POST" action="/admin/error-reports/{{ $r['id'] }}/fix" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-700 hover:text-green-900" title="Çözüldü olarak işaretle"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                @endif
                                <form method="POST" action="/admin/error-reports/{{ $r['id'] }}/delete" class="inline error-del">
                                    @csrf
                                    <button type="button" class="text-slate-400 hover:text-red-600" title="Sil"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

@section('scripts')
<script>
$(function(){
    if ($('#errorsTable tbody tr').length) {
        $('#errorsTable').DataTable({
            order: [[0, 'asc'], [5, 'desc']],
            columnDefs: [{ orderable: false, targets: [6] }],
        });
    }
    document.querySelectorAll('.error-del button').forEach(btn => btn.addEventListener('click', () => {
        const form = btn.closest('form');
        Swal.fire({ title:'Bu hata raporu silinsin mi?', icon:'warning', showCancelButton:true,
            confirmButtonColor:'#dc2626', confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç' })
            .then(r => r.isConfirmed && form.submit());
    }));
    document.querySelectorAll('.clear-fixed-form button').forEach(btn => btn.addEventListener('click', () => {
        const form = btn.closest('form');
        Swal.fire({ title:'Çözülmüş tüm hatalar silinsin mi?', icon:'warning', showCancelButton:true,
            confirmButtonColor:'#dc2626', confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç' })
            .then(r => r.isConfirmed && form.submit());
    }));
});
</script>
@endsection
