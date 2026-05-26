@extends('layouts.admin')

@section('title', 'Kullanıcılar')
@section('crumb', 'Kullanıcılar')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Kullanıcılar</h1>
        <p class="text-sm text-slate-500 mt-1">Toplam {{ count($users) }} kayıt</p>
    </div>
    <a href="/admin/users/create" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
        <i class="fa-solid fa-user-plus"></i> Yeni Kullanıcı
    </a>
</div>

@if(!$users)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-users text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Henüz kullanıcı yok.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="usersTable" class="display w-full">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Ad / E-posta</th>
                    <th style="width:130px">Rol</th>
                    <th style="width:140px">Kayıt</th>
                    <th style="width:160px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    @php
                        $pill = match($u->role ?? 'user') {
                            'superadmin' => ['bg-red-100', 'text-red-700'],
                            'admin'      => ['bg-brand-50', 'text-brand-700'],
                            default      => ['bg-slate-200', 'text-slate-600'],
                        };
                        $init = mb_strtoupper(mb_substr($u->name ?? 'U', 0, 1));
                    @endphp
                    <tr>
                        <td class="text-slate-400 font-mono text-xs">{{ $u->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white font-bold text-xs flex items-center justify-center">{{ $init }}</div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $pill[0] }} {{ $pill[1] }}">{{ $u->role ?? 'user' }}</span></td>
                        <td class="text-sm text-slate-500">{{ format_date($u->created_at) }}</td>
                        <td>
                            <div class="flex items-center gap-3 text-sm">
                                <a href="/admin/users/{{ $u->id }}/edit" class="text-brand-700 hover:text-brand-900 font-semibold"><i class="fa-solid fa-pen-to-square text-[12px]"></i> Düzenle</a>
                                <form method="post" action="/admin/users/{{ $u->id }}/delete" class="user-del">
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
    $('#usersTable').DataTable({ order:[[3,'desc']], columnDefs:[{orderable:false,targets:[1,4]}] });
    $('.user-del button').on('click', function(){
        const form = $(this).closest('form')[0];
        Swal.fire({
            title:'Kullanıcı silinsin mi?', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#64748b',
            confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç',
        }).then(r => r.isConfirmed && form.submit());
    });
});
</script>
@endsection
