@extends('layouts.admin')

@section('title', $mode === 'create' ? 'Yeni Blok' : 'Blok Düzenle')
@section('crumb', $mode === 'create' ? 'Editör · Yeni' : 'Editör · Düzenle')

@section('content')
<a href="/admin/editor" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Editöre dön
</a>

<form method="post" action="{{ $mode === 'create' ? '/admin/editor' : '/admin/editor/' . $block->id . '/update' }}"
      class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6 shadow-soft space-y-4">
    @csrf

    @if($mode === 'create')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Sayfa <span class="text-red-500">*</span></label>
                <input type="text" name="page" required placeholder="home"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">örn: home, blog, iletisim, hakkimizda</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Anahtar <span class="text-red-500">*</span></label>
                <input type="text" name="block_key" required placeholder="hero.title"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">örn: hero.title, hero.subtitle, about.body</p>
            </div>
        </div>
    @else
        <div class="grid md:grid-cols-[1fr_2fr] gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
            <div>
                <div class="text-[10.5px] uppercase tracking-widest font-bold text-slate-400">Sayfa</div>
                <div class="font-mono text-sm font-semibold text-brand-700 mt-0.5">{{ $block->page }}</div>
            </div>
            <div>
                <div class="text-[10.5px] uppercase tracking-widest font-bold text-slate-400">Anahtar</div>
                <div class="font-mono text-sm font-semibold text-brand-700 mt-0.5">{{ $block->block_key }}</div>
            </div>
        </div>
    @endif

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Etiket (opsiyonel, admin için)</label>
        <input type="text" name="label" value="{{ $block->label ?? '' }}" placeholder="Hero Başlık"
               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
    </div>

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tip</label>
        <select name="type" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            <option value="text"  {{ ($block->type ?? 'text') === 'text'  ? 'selected' : '' }}>text — düz metin (HTML escape edilir)</option>
            <option value="html"  {{ ($block->type ?? '') === 'html'  ? 'selected' : '' }}>html — ham HTML (escape edilmez)</option>
            <option value="image" {{ ($block->type ?? '') === 'image' ? 'selected' : '' }}>image — görsel URL</option>
        </select>
    </div>

    <div>
        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">İçerik</label>
        <textarea name="content" rows="10"
                  class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y font-mono">{{ $block->content ?? '' }}</textarea>
    </div>

    @php
        $samplePage = $block->page ?? 'page';
        $sampleKey  = $block->block_key ?? 'key';
    @endphp
    <div class="bg-blue-50 border border-blue-100 rounded-md p-3 text-[12.5px] text-blue-900">
        <strong>Kullanım:</strong> View'da şu helper'ı kullan
        <br>
        <code class="inline-block mt-1.5 bg-white px-2 py-1 rounded text-brand-700 font-mono text-[12px]">&#123;&#123; editable('{{ $samplePage }}.{{ $sampleKey }}') &#125;&#125;</code>
        @if(($block->type ?? '') === 'html')
            <div class="mt-1.5">HTML için: <code class="inline-block bg-white px-2 py-1 rounded text-brand-700 font-mono text-[12px]">&#123;!! editable('{{ $samplePage }}.{{ $sampleKey }}', null, 'html') !!&#125;</code></div>
        @endif
    </div>

    <div class="flex gap-2 pt-2 border-t border-slate-100">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
            <i class="fa-solid fa-check"></i> {{ $mode === 'create' ? 'Oluştur' : 'Güncelle' }}
        </button>
        <a href="/admin/editor" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 text-sm font-semibold rounded-md transition">Vazgeç</a>
    </div>
</form>
@endsection
