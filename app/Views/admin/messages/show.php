@extends('layouts.admin')

@section('title', $m->subject ?? 'Mesaj')
@section('crumb', 'Mesaj')

@section('content')
<a href="/admin/messages" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Mesajlara dön
</a>

<div class="max-w-3xl bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
    <div class="px-6 py-5 border-b border-slate-200">
        <h1 class="text-xl font-bold tracking-tight mb-4">{{ $m->subject ?? '(konusuz)' }}</h1>
        <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-[13px]">
            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Gönderen</dt>
            <dd>{{ $m->name }} &lt;<a href="mailto:{{ $m->email }}" class="text-brand-700 hover:underline">{{ $m->email }}</a>&gt;</dd>

            @if($m->phone)
            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Telefon</dt>
            <dd>{{ format_phone($m->phone) }}</dd>
            @endif

            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Tarih</dt>
            <dd>{{ format_datetime($m->created_at) }}</dd>

            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">IP</dt>
            <dd class="font-mono text-xs">{{ $m->ip ?? '-' }}</dd>
        </dl>
    </div>
    <div class="px-6 py-5 whitespace-pre-wrap leading-relaxed text-slate-700">{{ $m->body }}</div>
</div>

<div class="flex gap-2 mt-5 max-w-3xl">
    <a href="mailto:{{ $m->email }}?subject=Re: {{ $m->subject ?? 'Mesajınız' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md transition">
        <i class="fa-solid fa-reply"></i> Yanıtla
    </a>
    <form method="post" action="/admin/messages/{{ $m->id }}/delete" id="deleteMsg" class="inline">
        @csrf
        <button type="button" onclick="confirmDelete()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-500 text-sm font-semibold rounded-md transition">
            <i class="fa-solid fa-trash"></i> Sil
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Mesajı sil?',
        text: 'Bu işlem geri alınamaz.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, sil',
        cancelButtonText: 'Vazgeç',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    }).then(r => { if (r.isConfirmed) document.getElementById('deleteMsg').submit(); });
}
</script>
@endsection
