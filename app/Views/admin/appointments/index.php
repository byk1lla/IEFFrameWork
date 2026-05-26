@extends('layouts.admin')

@section('title', 'Randevular')
@section('crumb', 'Randevular')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Randevular</h1>
        <p class="text-sm text-slate-500 mt-1">Bekleyen: <b class="text-amber-700">{{ $summary['pending'] }}</b> · Onaylı: <b class="text-green-700">{{ $summary['confirmed'] }}</b> · Bugün: <b class="text-brand-700">{{ $summary['today'] }}</b> · Gelecek: <b class="text-slate-900">{{ $summary['upcoming'] }}</b></p>
    </div>
    <div class="inline-flex bg-slate-100 rounded-md p-0.5">
        @foreach(['all'=>'Tümü','pending'=>'Bekleyen','confirmed'=>'Onaylı','today'=>'Bugün','upcoming'=>'Gelecek','past'=>'Geçmiş'] as $k => $v)
            <a href="?filter={{ $k }}" class="px-3 py-1.5 rounded text-[13px] font-semibold {{ $filter === $k ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">{{ $v }}</a>
        @endforeach
    </div>
</div>

@php $statusInfo = [
    'pending'   => ['Bekliyor', 'bg-amber-100 text-amber-700'],
    'confirmed' => ['Onaylandı', 'bg-green-100 text-green-700'],
    'done'      => ['Tamamlandı','bg-slate-200 text-slate-700'],
    'cancelled' => ['İptal',     'bg-red-100 text-red-700'],
]; @endphp

@if(!$rows)
    <div class="bg-white border border-dashed border-slate-300 rounded-xl py-16 text-center">
        <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">Bu filtreyle kayıt yok.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <table id="apptTable" class="display w-full">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Müşteri</th>
                    <th>Servis</th>
                    <th style="width:160px">Tarih</th>
                    <th style="width:110px">Durum</th>
                    <th style="width:90px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    @php $st = $statusInfo[$r['status']] ?? ['?', 'bg-slate-100']; @endphp
                    <tr>
                        <td class="text-slate-400 font-mono text-xs">{{ $r['id'] }}</td>
                        <td>
                            <div class="font-semibold text-slate-900">{{ $r['customer_name'] }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $r['email'] }}</div>
                        </td>
                        <td>
                            <div class="text-[13px] font-medium">{{ $r['service_name'] ?? '—' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $r['duration_min'] ?? '?' }} dk</div>
                        </td>
                        <td class="text-sm">{{ format_datetime($r['start_at']) }}</td>
                        <td><span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $st[1] }}">{{ $st[0] }}</span></td>
                        <td><a href="/admin/appointments/{{ $r['id'] }}" class="text-brand-700 hover:text-brand-900 font-semibold text-sm">Aç →</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

@section('scripts')
<script>$(function(){ $('#apptTable').DataTable({ order:[[3,'desc']], columnDefs:[{orderable:false,targets:5}] }); });</script>
@endsection
