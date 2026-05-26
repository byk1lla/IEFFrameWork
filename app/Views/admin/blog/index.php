@extends('layouts.admin')

@section('title', 'Blog')
@section('crumb', 'Blog')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Blog Yazıları</h1>
        <p class="text-sm text-slate-500 mt-1">Toplam {{ count($posts) }} kayıt</p>
    </div>
    <a href="/admin/blog/create" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
        <i class="fa-solid fa-plus"></i> Yeni Yazı
    </a>
</div>

@if(!$posts)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-newspaper text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Henüz yazı yok.</p>
        <a href="/admin/blog/create" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-slate-900 text-white text-sm rounded-md hover:bg-black">İlk yazıyı oluştur</a>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="blogTable" class="display w-full">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th style="width:120px">Durum</th>
                    <th style="width:140px">Tarih</th>
                    <th style="width:160px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $p)
                    <tr>
                        <td>
                            <div class="font-semibold text-slate-900">{{ $p->title }}</div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">/{{ $p->slug }}</div>
                        </td>
                        <td>
                            @if($p->isPublished())
                                <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-green-100 text-green-700">Yayında</span>
                            @else
                                <span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-slate-200 text-slate-600">Taslak</span>
                            @endif
                        </td>
                        <td class="text-sm text-slate-500">{{ format_date($p->created_at) }}</td>
                        <td>
                            <div class="flex items-center gap-3 text-sm">
                                <a href="/admin/blog/{{ $p->id }}/edit" class="text-brand-700 hover:text-brand-900 font-semibold"><i class="fa-solid fa-pen-to-square text-[12px]"></i> Düzenle</a>
                                <form method="post" action="/admin/blog/{{ $p->id }}/delete" class="inline blog-del">
                                    @csrf
                                    <button type="button" class="text-slate-400 hover:text-red-600 font-semibold"><i class="fa-solid fa-trash text-[12px]"></i> Sil</button>
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
    $('#blogTable').DataTable({ order:[[2,'desc']], columnDefs:[{orderable:false,targets:3}] });
    $('.blog-del button').on('click', function(){
        const form = $(this).closest('form')[0];
        Swal.fire({
            title:'Yazı silinsin mi?', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#64748b',
            confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç',
        }).then(r => r.isConfirmed && form.submit());
    });
});
</script>
@endsection
