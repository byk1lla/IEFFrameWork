@extends('layouts.admin')

@section('title', $mode === 'create' ? 'Yeni Yazı' : 'Yazıyı Düzenle')
@section('crumb', $mode === 'create' ? 'Blog · Yeni' : 'Blog · Düzenle')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    /* Quill custom */
    .ql-toolbar.ql-snow { border: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; background: #fafafa; padding: 8px 10px; }
    .ql-container.ql-snow { border: 1px solid #e2e8f0; border-top: 0; border-radius: 0 0 8px 8px; min-height: 320px; font-family: 'Poppins', sans-serif; font-size: 14.5px; }
    .ql-editor { min-height: 320px; line-height: 1.65; }
    .ql-editor.ql-blank::before { font-style: normal; color: #94a3b8; }
</style>
@endsection

@section('content')
<a href="/admin/blog" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-4 transition">
    <i class="fa-solid fa-arrow-left text-[11px]"></i> Blog yönetimi
</a>

<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">{{ $mode === 'create' ? 'Yeni yazı yaz.' : $post->title }}</h1>
        <p class="text-sm text-slate-500 mt-1">Aşağıdaki alanları doldur, sağdan kaydet — veya AI ile sıfırdan oluştur.</p>
    </div>
    @if($mode === 'create')
        <button type="button" id="aiGenBtn"
                {{ empty($aiReady) ? 'disabled' : '' }}
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-md transition shadow-sm
                       {{ !empty($aiReady) ? 'bg-gradient-to-r from-amber-500 to-pink-600 hover:from-amber-600 hover:to-pink-700 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                title="{{ !empty($aiReady) ? 'Groq AI ile yazı üret' : 'Groq API key tanımlanmamış. Ayarlar → AI sekmesi.' }}">
            <i class="fa-solid fa-wand-magic-sparkles"></i> AI ile Oluştur
        </button>
    @endif
</div>

<form method="post" id="postForm"
      action="{{ $mode === 'create' ? '/admin/blog' : '/admin/blog/' . $post->id . '/update' }}"
      enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">
    @csrf

    {{-- ═══ Sol: Ana içerik ═══════════════════════════════════════ --}}
    <div class="space-y-5">

        {{-- Başlık --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-soft">
            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Başlık <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="postTitle" required value="{{ $post->title ?? '' }}" placeholder="Yazı başlığı"
                   class="w-full px-0 py-2 border-0 border-b-2 border-slate-200 text-2xl font-extrabold tracking-tight focus:outline-none focus:border-brand-600 placeholder:text-slate-300 transition">
        </div>

        {{-- URL slug --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-soft">
            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">URL Slug</label>
            <div class="flex items-center gap-2">
                <span class="text-slate-400 font-mono text-sm">/blog/</span>
                <input type="text" name="slug" id="postSlug" value="{{ $post->slug ?? '' }}" placeholder="otomatik oluşturulur"
                       class="flex-1 px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <p class="text-[11.5px] text-slate-400 mt-2">Boş bırakırsan başlıktan otomatik üretilir.</p>
        </div>

        {{-- Özet --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-soft">
            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Özet</label>
            <textarea name="excerpt" id="postExcerpt" rows="3" maxlength="500" placeholder="Listede ve SEO özet metninde görünür."
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $post->excerpt ?? '' }}</textarea>
        </div>

        {{-- İçerik (Quill) --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-soft">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500">İçerik <span class="text-red-500">*</span></label>
                <span class="text-[11px] text-slate-400">Quill zengin metin editörü</span>
            </div>
            <input type="hidden" name="content" id="postContent" value="{{ $post->content ?? '' }}">
            <div id="quillEditor"></div>
        </div>

        {{-- SEO Ayarları --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-soft">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 bg-brand-50 text-brand-700 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass-chart text-[14px]"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold tracking-tight">SEO Ayarları</h3>
                    <p class="text-xs text-slate-500">Görünürlük</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">SEO Başlık</label>
                    <input type="text" name="seo_title" id="postSeoTitle" maxlength="191" value="{{ $post->seo_title ?? '' }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    <p class="text-[11.5px] text-slate-400 mt-1.5">Boş bırakırsan başlık kullanılır.</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">SEO Açıklama</label>
                    <textarea name="seo_description" id="postSeoDesc" rows="2" maxlength="300"
                              class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $post->seo_description ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Anahtar Kelimeler (virgülle)</label>
                    <input type="text" name="seo_keywords" id="postSeoKw" maxlength="300" value="{{ $post->seo_keywords ?? '' }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Sağ: Sidebar (yayın) ═══════════════════════════════════ --}}
    <aside class="space-y-4">
        <div class="bg-slate-900 text-white rounded-xl p-5 shadow-card sticky top-20">
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">Yayın</div>

            <div class="mb-4">
                <label class="block text-[10.5px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Durum</label>
                <select name="published" id="postStatus"
                        class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-md text-[14px] text-white focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20 transition">
                    <option value="0" {{ !($post->published ?? 0) ? 'selected' : '' }}>Taslak</option>
                    <option value="1" {{  ($post->published ?? 0) ? 'selected' : '' }}>Yayında</option>
                </select>
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer mb-4 px-3 py-2 bg-slate-800 rounded-md hover:bg-slate-700 transition">
                <input type="checkbox" name="is_featured" value="1" {{ ($post->is_featured ?? 0) ? 'checked' : '' }} class="w-4 h-4 accent-amber-500 cursor-pointer">
                <i class="fa-solid fa-star text-amber-400 text-[13px]"></i>
                <span class="text-sm font-medium">Öne çıkan yazı</span>
            </label>

            <div class="mb-4">
                <label class="block text-[10.5px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-md text-[14px] text-white focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20 transition">
                    <option value="" class="text-slate-500">— Seçilmedi —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ ($post->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-[10.5px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Kapak Görseli URL</label>
                <input type="text" name="cover_image" value="{{ $post->cover_image ?? '' }}" placeholder="/uploads/blog/..."
                       class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-md text-[13px] text-white placeholder:text-slate-600 font-mono focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20 transition">
                @if($post->cover_image ?? null)
                    <div class="mt-2"><img src="{{ $post->coverUrl() }}" class="h-20 rounded border border-slate-700"></div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-[10.5px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">veya yükle</label>
                <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp,.gif"
                       class="w-full text-[12px] text-slate-300 file:mr-3 file:px-3 file:py-1.5 file:rounded file:border-0 file:bg-slate-700 file:text-white file:font-semibold file:text-xs hover:file:bg-slate-600 cursor-pointer">
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-bold rounded-md shadow-lg transition-all">
                <i class="fa-solid fa-floppy-disk"></i> Kaydet
            </button>
        </div>
    </aside>
</form>

{{-- AI Modal --}}
<div id="aiModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-3 bg-gradient-to-r from-amber-500/10 to-pink-500/10">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-pink-600 text-white rounded-lg flex items-center justify-center shadow-md">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold tracking-tight">AI ile Yazı Oluştur</h3>
                <p class="text-xs text-slate-500">Groq'a konuyu söyle, başlık + özet + içerik + SEO üretsin.</p>
            </div>
            <button type="button" onclick="closeAiModal()" class="w-8 h-8 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-md flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6">
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Konu / Brief</label>
            <textarea id="aiTopic" rows="4" placeholder="Örn: Direksiyon başında dikkat etmeniz gereken 5 şey — Türkçe, samimi ton, ehliyet adayları için..."
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y"></textarea>
            <p class="text-[11.5px] text-slate-400 mt-1.5">Üretim 10-20 saniye sürebilir.</p>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
            <button type="button" onclick="closeAiModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">İptal</button>
            <button type="button" id="aiGenSubmit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-pink-600 hover:from-amber-600 hover:to-pink-700 text-white text-sm font-bold rounded-md shadow-sm transition">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span id="aiGenText">Üret</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
// ─── Quill editör ──────────────────────────────────────────────
const quill = new Quill('#quillEditor', {
    theme: 'snow',
    placeholder: 'Yazı içeriği...',
    modules: {
        toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            [{ align: [] }],
            ['clean'],
        ],
    },
});
// Set initial content
const initial = document.getElementById('postContent').value || '';
if (initial) {
    if (initial.startsWith('<')) quill.clipboard.dangerouslyPasteHTML(initial);
    else quill.setText(initial);
}
// Form submit'te HTML'i hidden input'a yaz
document.getElementById('postForm').addEventListener('submit', () => {
    document.getElementById('postContent').value = quill.root.innerHTML.trim();
});

// ─── AI ile Oluştur ────────────────────────────────────────────
const aiBtn = document.getElementById('aiGenBtn');
if (aiBtn) {
    aiBtn.addEventListener('click', () => {
        if (aiBtn.disabled) return;
        document.getElementById('aiModal').classList.remove('hidden');
        document.getElementById('aiModal').classList.add('flex');
        setTimeout(() => document.getElementById('aiTopic').focus(), 100);
    });
}
function closeAiModal() {
    const m = document.getElementById('aiModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
document.getElementById('aiGenSubmit')?.addEventListener('click', async () => {
    const topic = document.getElementById('aiTopic').value.trim();
    if (topic.length < 6) {
        Swal.fire({ icon:'warning', title:'Konu çok kısa', text:'En az 6 karakter yaz.', confirmButtonColor:'#1e40af' });
        return;
    }
    const btn  = document.getElementById('aiGenSubmit');
    const text = document.getElementById('aiGenText');
    btn.disabled = true; text.textContent = 'Üretiliyor…';
    btn.classList.add('opacity-60', 'cursor-not-allowed');

    try {
        const csrf = document.querySelector('meta[name=csrf-token]')?.content || '';
        const fd = new FormData();
        fd.append('_csrf_token', csrf);
        fd.append('topic', topic);
        const res = await fetch('/admin/blog/ai/generate', { method:'POST', body: fd });
        const data = await res.json();
        if (!data.ok) throw new Error(data.error || 'Hata');

        // Form alanlarını doldur
        document.getElementById('postTitle').value    = data.post.title || '';
        document.getElementById('postExcerpt').value  = data.post.excerpt || '';
        document.getElementById('postSeoTitle').value = data.post.seo_title || '';
        document.getElementById('postSeoDesc').value  = data.post.seo_description || '';
        document.getElementById('postSeoKw').value    = data.post.seo_keywords || '';
        // Markdown-ish → Quill'e text olarak (basit: \n korunur, ## başlık paragraf'a)
        quill.setText(data.post.content || '');

        closeAiModal();
        Swal.fire({ icon:'success', title:'Hazır!', text:'Üretildi. İçeriği düzenleyip kaydedebilirsin.', timer:2200, showConfirmButton:false });
    } catch (e) {
        Swal.fire({ icon:'error', title:'Üretim başarısız', text: e.message || 'Bilinmeyen hata', confirmButtonColor:'#dc2626' });
    } finally {
        btn.disabled = false; text.textContent = 'Üret';
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
});
</script>
@endsection
