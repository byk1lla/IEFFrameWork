@extends('layouts.admin')

@section('title', 'Mesajlar')
@section('crumb', 'Mesajlar')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">İletişim Mesajları</h1>
        <p class="text-sm text-slate-500 mt-1">Toplam <b class="text-slate-900">{{ $total }}</b> mesaj · <b class="text-amber-600">{{ $unreadCount }}</b> okunmamış</p>
    </div>
    <div class="inline-flex bg-slate-100 rounded-md p-0.5">
        <a href="/admin/messages" class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter==='all' ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">Tümü</a>
        <a href="/admin/messages?filter=unread" class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter==='unread' ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">Okunmamış</a>
    </div>
</div>

@if(!$messages)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-inbox text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Henüz mesaj yok.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="messagesTable" class="display w-full">
            <thead>
                <tr>
                    <th style="width:24px"></th>
                    <th>Gönderen</th>
                    <th>Konu</th>
                    <th style="width:140px">Tarih</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $m)
                    <tr class="{{ !$m->is_read ? 'bg-amber-50/30' : '' }}">
                        <td class="text-center">
                            @if(!$m->is_read)
                                <span class="inline-block w-2 h-2 rounded-full bg-accent-500"></span>
                            @endif
                        </td>
                        <td>
                            <div class="font-{{ $m->is_read ? 'medium' : 'bold' }} text-slate-900">{{ $m->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $m->email }}</div>
                        </td>
                        <td>
                            <a href="/admin/messages/{{ $m->id }}" class="text-slate-900 font-{{ $m->is_read ? 'normal' : 'semibold' }} hover:text-brand-700">
                                {{ $m->subject ?? '(konusuz)' }}
                            </a>
                        </td>
                        <td class="text-sm text-slate-500">{{ format_datetime($m->created_at) }}</td>
                        <td class="text-right">
                            <a href="/admin/messages/{{ $m->id }}" class="text-brand-700 hover:text-brand-900 text-sm font-semibold">Aç <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
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
    $('#messagesTable').DataTable({
        order: [[3, 'desc']],
        columnDefs: [{ orderable: false, targets: [0, 4] }],
    });
});
</script>
@endsection
