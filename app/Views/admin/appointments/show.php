@extends('layouts.admin')

@section('title', 'Randevu #' . $appt->id)
@section('crumb', 'Randevu #' . $appt->id)

@section('content')
<a href="/admin/appointments" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Randevulara dön
</a>

@php $statusInfo = [
    'pending'   => ['Bekliyor', 'bg-amber-100 text-amber-700'],
    'confirmed' => ['Onaylandı', 'bg-green-100 text-green-700'],
    'done'      => ['Tamamlandı','bg-slate-200 text-slate-700'],
    'cancelled' => ['İptal',     'bg-red-100 text-red-700'],
];
$st = $statusInfo[$appt->status] ?? ['?', 'bg-slate-100']; @endphp

<div class="grid lg:grid-cols-[2fr_1fr] gap-5 max-w-5xl">

    {{-- Sol: Detaylar --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight">{{ $appt->customer_name }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ format_datetime($appt->start_at) }} — {{ format_datetime($appt->end_at, 'H:i') }}</p>
            </div>
            <span class="text-[10.5px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $st[1] }}">{{ $st[0] }}</span>
        </div>
        <dl class="px-6 py-5 grid sm:grid-cols-2 gap-x-6 gap-y-4 text-[13px]">
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Servis</dt><dd class="font-semibold text-slate-900 mt-0.5">{{ $service?->name ?? '—' }} ({{ $service?->duration_min ?? '?' }} dk)</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Fiyat</dt><dd class="font-semibold text-slate-900 mt-0.5">{{ $service ? format_money($service->price) : '—' }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">E-posta</dt><dd class="mt-0.5"><a href="mailto:{{ $appt->email }}" class="text-brand-700 hover:underline">{{ $appt->email }}</a></dd></div>
            @if($appt->phone)
                <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Telefon</dt><dd class="mt-0.5"><a href="tel:{{ preg_replace('/[^0-9+]/','',$appt->phone) }}" class="text-brand-700 hover:underline">{{ format_phone($appt->phone) }}</a></dd></div>
            @endif
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">Talep tarihi</dt><dd class="mt-0.5">{{ format_datetime($appt->created_at) }}</dd></div>
            <div><dt class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold">IP</dt><dd class="mt-0.5 font-mono text-xs">{{ $appt->ip ?? '—' }}</dd></div>
        </dl>

        @if($appt->notes)
            <div class="px-6 py-5 border-t border-slate-100">
                <div class="text-[10.5px] uppercase tracking-widest text-slate-400 font-bold mb-2">Müşteri Notu</div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $appt->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Sağ: Durum güncelle --}}
    <aside class="space-y-4">
        <form method="post" action="/admin/appointments/{{ $appt->id }}/status" class="bg-white border border-slate-200 rounded-xl p-5 shadow-soft">
            @csrf
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Durumu Değiştir</h3>
            <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition mb-3">
                @foreach(['pending'=>'Bekliyor','confirmed'=>'Onaylandı','done'=>'Tamamlandı','cancelled'=>'İptal'] as $k => $v)
                    <option value="{{ $k }}" {{ $appt->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5 mt-3">Admin Notu</label>
            <textarea name="admin_notes" rows="3" placeholder="İç not (opsiyonel)" class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y mb-3">{{ $appt->admin_notes ?? '' }}</textarea>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md transition"><i class="fa-solid fa-check"></i> Kaydet</button>
        </form>

        <form method="post" action="/admin/appointments/{{ $appt->id }}/delete" class="appt-del">
            @csrf
            <button type="button" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-200 hover:border-red-500 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-md transition"><i class="fa-solid fa-trash"></i> Randevuyu Sil</button>
        </form>
    </aside>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.appt-del button').forEach(b => b.addEventListener('click', () => {
    const f = b.closest('form');
    Swal.fire({ title:'Randevu silinsin mi?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#dc2626', confirmButtonText:'Evet, sil', cancelButtonText:'Vazgeç' })
        .then(r => r.isConfirmed && f.submit());
}));
</script>
@endsection
