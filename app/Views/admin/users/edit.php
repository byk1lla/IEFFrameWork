@extends('layouts.admin')

@section('title', $mode === 'create' ? 'Yeni Kullanıcı' : 'Kullanıcı Düzenle')
@section('crumb', $mode === 'create' ? 'Kullanıcılar · Yeni' : 'Kullanıcılar · Düzenle')

@section('content')
<a href="/admin/users" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Kullanıcılara dön
</a>

<form method="post" action="{{ $mode === 'create' ? '/admin/users' : '/admin/users/' . $user->id . '/update' }}"
      class="max-w-2xl bg-white border border-slate-200 rounded-xl p-6 shadow-soft space-y-4">
    @csrf

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Ad Soyad <span class="text-red-500">*</span></label>
        <input type="text" name="name" required value="{{ $user->name ?? '' }}"
               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
    </div>

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">E-posta <span class="text-red-500">*</span></label>
        <input type="email" name="email" required value="{{ $user->email ?? '' }}"
               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
    </div>

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">
            Şifre {{ $mode === 'create' ? '*' : '' }}
            @if($mode !== 'create')<span class="text-xs font-normal text-slate-400 ml-1">(değiştirmek istemiyorsan boş bırak)</span>@endif
        </label>
        <input type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} minlength="6" autocomplete="new-password"
               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
    </div>

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Rol <span class="text-red-500">*</span></label>
        <select name="role" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            <option value="user"       {{ ($user->role ?? '') === 'user' ? 'selected' : '' }}>user — Standart kullanıcı</option>
            <option value="admin"      {{ ($user->role ?? '') === 'admin' ? 'selected' : '' }}>admin — Admin yetkisi</option>
            <option value="superadmin" {{ ($user->role ?? '') === 'superadmin' ? 'selected' : '' }}>superadmin — Tam yetki</option>
        </select>
    </div>

    <div class="flex gap-2 pt-2 border-t border-slate-100">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
            <i class="fa-solid fa-check"></i> {{ $mode === 'create' ? 'Oluştur' : 'Güncelle' }}
        </button>
        <a href="/admin/users" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">Vazgeç</a>
    </div>
</form>
@endsection
