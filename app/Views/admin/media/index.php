@extends('layouts.admin')

@section('title', 'Medya')
@section('crumb', 'Medya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Medya / Galeri</h1>
    <p class="text-sm text-slate-500 mt-1">Toplam {{ $total }} dosya</p>
</div>

<form method="post" action="/admin/media" enctype="multipart/form-data"
      class="bg-white border border-slate-200 rounded-xl p-5 mb-6 shadow-soft">
    @csrf
    <div class="grid md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Dosya</label>
            <input type="file" name="file" required accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                   class="w-full px-3 py-2 border border-slate-300 rounded-md text-[13.5px] file:mr-3 file:px-3 file:py-1 file:rounded file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold file:text-xs hover:file:bg-slate-200">
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Alternatif metin</label>
            <input type="text" name="alt" maxlength="255"
                   class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition self-end">
            <i class="fa-solid fa-upload"></i> Yükle
        </button>
    </div>
    <p class="text-xs text-slate-400 mt-2.5">İzinli formatlar: jpg, png, gif, webp, svg, pdf, doc(x), xls(x), zip</p>
</form>

@if(!$items)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-images text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Henüz dosya yok. Yukarıdan ilk yüklemeyi yap.</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($items as $m)
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden group hover:border-slate-900 hover:shadow-card transition">
                <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                    @if($m->isImage())
                        <img src="{{ $m->url() }}" alt="{{ e($m->alt ?? $m->original_name) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                        <div class="text-center p-3">
                            <i class="fa-solid fa-file-lines text-3xl text-slate-300"></i>
                            <div class="font-mono text-[10px] text-slate-500 mt-2 uppercase">{{ pathinfo($m->original_name, PATHINFO_EXTENSION) }}</div>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <div class="text-[12px] font-semibold truncate" title="{{ e($m->original_name) }}">{{ $m->original_name }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $m->humanSize() }}</div>
                    <div class="flex items-center justify-between mt-2.5">
                        <a href="{{ $m->url() }}" target="_blank" class="text-[12px] text-brand-700 hover:text-brand-900 font-semibold"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Aç</a>
                        <form method="post" action="/admin/media/{{ $m->id }}/delete" class="media-del">
                            @csrf
                            <button type="button" class="text-[12px] text-slate-400 hover:text-red-600 font-semibold"><i class="fa-solid fa-trash text-[10px]"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@section('scripts')
<script>
document.querySelectorAll('.media-del button').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = btn.closest('form');
        Swal.fire({
            title:'Dosya silinsin mi?', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#64748b',
            confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç',
        }).then(r => r.isConfirmed && form.submit());
    });
});
</script>
@endsection
