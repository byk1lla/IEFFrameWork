@extends('layouts.admin')

@section('title', 'Editör · Bloklar')
@section('crumb', 'Editör · Bloklar')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Manuel Bloklar</h1>
        <p class="text-sm text-slate-500 mt-1">Toplam {{ $total }} blok · View'da <code class="bg-slate-100 px-2 py-0.5 rounded text-brand-700 text-xs font-mono">editable('home.hero.title')</code> ile çağır</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="/admin/editor" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-slate-900 text-slate-700 hover:text-slate-900 text-sm font-semibold rounded-md transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i> Sayfalar
        </a>
        <a href="/admin/editor/blocks/create" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
            <i class="fa-solid fa-plus"></i> Yeni Blok
        </a>
    </div>
</div>

@if(!$blocks)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-pen-ruler text-3xl text-slate-300 mb-3"></i>
        <h3 class="text-base font-bold mb-1">Henüz blok yok</h3>
        <p class="text-sm text-slate-500 mb-4">İlk bloğu inline editör'den veya manuel oluştur.</p>
        <a href="/admin/editor" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md transition">
            <i class="fa-solid fa-arrow-left"></i> Sayfalara dön
        </a>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="editorBlocksTable" class="display w-full">
            <thead>
                <tr>
                    <th style="width:100px">Sayfa</th>
                    <th>Anahtar</th>
                    <th>Etiket / İçerik</th>
                    <th style="width:80px">Tip</th>
                    <th style="width:140px">Güncelleme</th>
                    <th style="width:140px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blocks as $b)
                    @php $tColor = match($b->type) {
                        'html'  => 'bg-violet-50 text-violet-700',
                        'image' => 'bg-pink-50 text-pink-700',
                        'icon'  => 'bg-cyan-50 text-cyan-700',
                        default => 'bg-slate-100 text-slate-700',
                    }; @endphp
                    <tr>
                        <td><span class="text-[10.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-brand-50 text-brand-700">{{ $b->page }}</span></td>
                        <td><code class="text-[12.5px] text-brand-700 font-mono font-semibold">{{ $b->block_key }}</code></td>
                        <td>
                            @if($b->label)<div class="text-[13px] font-semibold text-slate-900">{{ $b->label }}</div>@endif
                            <div class="text-[12.5px] text-slate-500 truncate max-w-md">{{ str_limit(strip_tags((string)$b->content) ?: '—', 80) }}</div>
                        </td>
                        <td><span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded {{ $tColor }}">{{ $b->type }}</span></td>
                        <td class="text-sm text-slate-500">{{ format_date($b->updated_at) }}</td>
                        <td>
                            <div class="flex items-center gap-3 text-sm">
                                <a href="/admin/editor/blocks/{{ $b->id }}/edit" class="text-brand-700 hover:text-brand-900 font-semibold"><i class="fa-solid fa-pen-to-square text-[12px]"></i> Düzenle</a>
                                <form method="post" action="/admin/editor/blocks/{{ $b->id }}/delete" class="inline editor-del">
                                    @csrf
                                    <button type="button" class="text-slate-400 hover:text-red-600 font-semibold"><i class="fa-solid fa-trash text-[12px]"></i></button>
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
    $('#editorBlocksTable').DataTable({
        order: [[0, 'asc'], [1, 'asc']],
        columnDefs: [{ orderable: false, targets: [5] }],
    });
    document.querySelectorAll('.editor-del button').forEach(btn => btn.addEventListener('click', () => {
        const form = btn.closest('form');
        Swal.fire({ title:'Blok silinsin mi?', icon:'warning', showCancelButton:true,
            confirmButtonColor:'#dc2626', confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç' })
            .then(r => r.isConfirmed && form.submit());
    }));
});
</script>
@endsection
