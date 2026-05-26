@extends('layouts.admin')

@section('title', 'Ayarlar')
@section('crumb', 'Ayarlar')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Site Ayarları</h1>
    <p class="text-sm text-slate-500 mt-1">Genel bilgiler, mail servis ve SEO ayarları.</p>
</div>

<div class="inline-flex flex-wrap gap-1 bg-slate-100 rounded-md p-0.5 mb-5">
    @php $tabs = [
        'general'    => ['Genel',     'fa-sliders'],
        'social'     => ['Sosyal',    'fa-share-nodes'],
        'appearance' => ['Görünüm',   'fa-palette'],
        'mail'       => ['Mail',      'fa-envelope'],
        'seo'        => ['SEO',       'fa-magnifying-glass-chart'],
        'security'   => ['Güvenlik',  'fa-shield-halved'],
        'ai'         => ['AI',        'fa-wand-magic-sparkles'],
    ]; @endphp
    @foreach($tabs as $k => $v)
        @php $href = $k === 'general' ? '/admin/settings' : '/admin/settings/' . $k; @endphp
        <a href="{{ $href }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded text-[13px] font-semibold transition {{ $tab === $k ? 'bg-white shadow-soft text-slate-900' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fa-solid {{ $v[1] }} text-[12px]"></i> {{ $v[0] }}
        </a>
    @endforeach
</div>

<form method="post" action="/admin/settings" class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6 shadow-soft space-y-4">
    @csrf
    <input type="hidden" name="_tab" value="{{ $tab }}">

    @if($tab === 'general')
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">Genel Bilgiler</h3>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Site Adı</label>
            <input type="text" name="general_site_name" value="{{ $general['site_name'] ?? config('app.name') }}"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Site Sloganı</label>
            <input type="text" name="general_site_tagline" value="{{ $general['site_tagline'] ?? '' }}"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">İletişim E-posta</label>
                <input type="email" name="general_contact_email" value="{{ $general['contact_email'] ?? '' }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Telefon</label>
                <input type="text" name="general_contact_phone" value="{{ $general['contact_phone'] ?? '' }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Adres</label>
            <textarea name="general_address" rows="2" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $general['address'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">WhatsApp Numarası (floating button için)</label>
            <input type="text" name="general_whatsapp_number" value="{{ $general['whatsapp_number'] ?? '' }}" placeholder="+90 555 111 22 33"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            <p class="text-[11.5px] text-slate-400 mt-1.5"><i class="fa-brands fa-whatsapp text-[#25d366] mr-1"></i> Boş bırakırsan buton gözükmez.</p>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Lokalizasyon & Davranış</h4>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Zaman Dilimi</label>
                <select name="general_timezone" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    @php $tz = $general['timezone'] ?? config('app.timezone', 'Europe/Istanbul'); @endphp
                    @foreach(['Europe/Istanbul','UTC','Europe/London','Europe/Berlin','America/New_York','America/Los_Angeles','Asia/Dubai','Asia/Tokyo'] as $opt)
                        <option value="{{ $opt }}" {{ $tz === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Varsayılan Dil</label>
                <select name="general_locale" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    @php $loc = $general['locale'] ?? config('app.locale', 'tr'); @endphp
                    <option value="tr" {{ $loc === 'tr' ? 'selected' : '' }}>Türkçe (tr)</option>
                    <option value="en" {{ $loc === 'en' ? 'selected' : '' }}>English (en)</option>
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Sayfa Başına Kayıt</label>
                <input type="number" name="general_per_page" value="{{ $general['per_page'] ?? 20 }}" min="5" max="200"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Blog/listeler için varsayılan</p>
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Sistem</h4>

        <label class="flex items-start gap-3 p-3.5 border-2 border-slate-200 hover:border-amber-300 rounded-md cursor-pointer transition {{ !empty($general['maintenance']) ? 'border-amber-400 bg-amber-50' : '' }}" id="maintenanceCard">
            <input type="checkbox" name="general_maintenance" value="1" {{ !empty($general['maintenance']) ? 'checked' : '' }} class="w-4 h-4 accent-amber-600 cursor-pointer mt-0.5" onchange="document.getElementById('maintFields').style.display = this.checked ? 'block' : 'none'; this.closest('label').classList.toggle('border-amber-400', this.checked); this.closest('label').classList.toggle('bg-amber-50', this.checked);">
            <div class="flex-1">
                <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    Bakım modu
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">Aktif olduğunda <strong>admin login olmayan</strong> tüm ziyaretçilere bakım sayfası gösterilir (HTTP 503). Admin paneli ve login sayfası her zaman erişilebilir.</div>
            </div>
        </label>

        <div id="maintFields" style="display: {{ !empty($general['maintenance']) ? 'block' : 'none' }};" class="ml-8 mt-1 space-y-3 pl-4 border-l-2 border-amber-200">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Özel Mesaj <span class="text-slate-400 font-normal">(opsiyonel)</span></label>
                <textarea name="general_maintenance_message" rows="2" placeholder="Sistemimizi geliştirmek için kısa bir bakım yapıyoruz. En kısa sürede tekrar hizmetinizdeyiz."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition resize-y">{{ $general['maintenance_message'] ?? '' }}</textarea>
                <p class="text-[11.5px] text-slate-400 mt-1">Boş bırakırsan varsayılan mesaj gösterilir.</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tahmini Açılış Zamanı <span class="text-slate-400 font-normal">(opsiyonel)</span></label>
                <input type="text" name="general_maintenance_eta" value="{{ $general['maintenance_eta'] ?? '' }}" placeholder="Bugün 18:00 · 25 Mayıs Pazar"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Serbest metin — kullanıcıya saat tahmini ver.</p>
            </div>
            <a href="/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 text-[12.5px] font-semibold rounded-md transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i> Bakım sayfasını yeni sekmede aç (incognito)
            </a>
        </div>

        <label class="inline-flex items-center gap-2.5 cursor-pointer pt-2">
            <input type="checkbox" name="general_debug_bar" value="1" {{ !empty($general['debug_bar']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer">
            <span class="text-sm font-medium text-slate-900">Debug bar (alttaki sorgu/route/latency çubuğu — sadece debug modunda)</span>
        </label>

    @elseif($tab === 'social')
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">Sosyal Medya Linkleri</h3>
        <p class="text-[12.5px] text-slate-500 -mt-2 mb-4">Footer'da ve site genelinde kullanılır. Boş bıraktıklarını gizlenir.</p>

        @php $socials = [
            ['facebook',  'Facebook',   'fa-brands fa-facebook',  '#1877f2', 'https://facebook.com/...'],
            ['instagram', 'Instagram',  'fa-brands fa-instagram', '#e1306c', 'https://instagram.com/...'],
            ['twitter',   'X (Twitter)','fa-brands fa-x-twitter', '#000',    'https://x.com/...'],
            ['linkedin',  'LinkedIn',   'fa-brands fa-linkedin',  '#0a66c2', 'https://linkedin.com/in/...'],
            ['youtube',   'YouTube',    'fa-brands fa-youtube',   '#ff0000', 'https://youtube.com/@...'],
            ['github',    'GitHub',     'fa-brands fa-github',    '#181717', 'https://github.com/...'],
            ['tiktok',    'TikTok',     'fa-brands fa-tiktok',    '#000',    'https://tiktok.com/@...'],
        ]; @endphp
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($socials as $s)
                @php
                    $key   = $s[0];
                    $label = $s[1];
                    $icon  = $s[2];
                    $color = $s[3];
                    $ph    = $s[4];
                @endphp
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">
                        <i class="{{ $icon }} mr-1.5" style="color:<?= e($color) ?>"></i> {{ $label }}
                    </label>
                    <input type="url" name="social_{{ $key }}" value="{{ $social[$key] ?? '' }}" placeholder="{{ $ph }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                </div>
            @endforeach
        </div>
        <label class="inline-flex items-center gap-2.5 cursor-pointer pt-3 mt-3 border-t border-slate-100">
            <input type="checkbox" name="social_show_in_footer" value="1" {{ !empty($social['show_in_footer']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer">
            <span class="text-sm font-medium text-slate-900">Footer'da göster</span>
        </label>

    @elseif($tab === 'appearance')
        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-red-50 to-amber-50 border border-amber-200 rounded-xl mb-5">
            <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-red-600 to-amber-500 text-white flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-palette text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-base font-bold text-slate-900 mb-0.5">Görünüm artık Site Editör'de</h4>
                <p class="text-[13px] text-slate-700 leading-relaxed">Logo, favicon, renkler ve footer metinleri site önizlemesi açıkken sağdaki <strong>Tema</strong> panelinden canlı düzenlenir.</p>
                <a href="/site-editor" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-slate-900 hover:bg-black text-white text-[13px] font-semibold rounded-md transition">
                    <i class="fa-solid fa-arrow-right"></i> Site Editör'ü Aç
                </a>
            </div>
        </div>

        <details class="bg-white border border-slate-200 rounded-xl">
            <summary class="px-5 py-4 cursor-pointer text-sm font-semibold text-slate-700 hover:text-slate-900 select-none">
                <i class="fa-solid fa-gear text-[12px] mr-2 text-slate-400"></i> Yine de buradan düzenle (klasik form)
            </summary>
            <div class="p-5 border-t border-slate-100 space-y-4">
        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Logo & Görsel</h4>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Logo URL (açık tema)</label>
                <input type="text" name="appearance_logo_url" value="{{ $appearance['logo_url'] ?? '' }}" placeholder="/assets/img/logo.svg"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Logo URL (koyu tema)</label>
                <input type="text" name="appearance_logo_dark_url" value="{{ $appearance['logo_dark_url'] ?? '' }}" placeholder="/assets/img/logo-dark.svg"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Favicon URL</label>
                <input type="text" name="appearance_favicon_url" value="{{ $appearance['favicon_url'] ?? '' }}" placeholder="/favicon.ico"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Varsayılan OG Görsel</label>
                <input type="text" name="appearance_og_default_url" value="{{ $appearance['og_default_url'] ?? '' }}" placeholder="/assets/img/og.png"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Renk & Tipografi</h4>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Primary Color</label>
                <div class="flex gap-2">
                    <input type="color" name="appearance_primary_color" value="{{ $appearance['primary_color'] ?? '#1d4ed8' }}" class="h-11 w-14 border border-slate-300 rounded-md cursor-pointer">
                    <input type="text" value="{{ $appearance['primary_color'] ?? '#1d4ed8' }}" oninput="this.previousElementSibling.value=this.value" class="flex-1 px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] font-mono">
                </div>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Accent Color</label>
                <div class="flex gap-2">
                    <input type="color" name="appearance_accent_color" value="{{ $appearance['accent_color'] ?? '#06b6d4' }}" class="h-11 w-14 border border-slate-300 rounded-md cursor-pointer">
                    <input type="text" value="{{ $appearance['accent_color'] ?? '#06b6d4' }}" oninput="this.previousElementSibling.value=this.value" class="flex-1 px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] font-mono">
                </div>
            </div>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Font Ailesi</label>
            <select name="appearance_font_family" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                @php $ff = $appearance['font_family'] ?? 'Poppins'; @endphp
                @foreach(['Poppins','Inter','Roboto','Open Sans','Manrope','Plus Jakarta Sans','Nunito','Lato'] as $f)
                    <option value="{{ $f }}" {{ $ff === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
            <p class="text-[11.5px] text-slate-400 mt-1">Google Fonts üzerinden otomatik yüklenir.</p>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Footer Metinleri</h4>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Footer Tanıtım Metni</label>
            <textarea name="appearance_footer_text" rows="2" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $appearance['footer_text'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Telif Hakkı (alt satır)</label>
            <input type="text" name="appearance_copyright_text" value="{{ $appearance['copyright_text'] ?? '' }}" placeholder="© {year} {site_name} · Tüm hakları saklıdır"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            <p class="text-[11.5px] text-slate-400 mt-1"><code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">{year}</code> ve <code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">{site_name}</code> placeholder'ları desteklenir.</p>
        </div>
            </div>
        </details>

    @elseif($tab === 'mail')
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">Mail Servisi</h3>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Driver</label>
            <select name="mail_driver" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <option value="log"  {{ ($mail['driver'] ?? 'log') === 'log'  ? 'selected' : '' }}>log — dev için (mail göndermez, storage/logs'a yazar)</option>
                <option value="mail" {{ ($mail['driver'] ?? '') === 'mail' ? 'selected' : '' }}>mail — PHP mail() (shared hosting)</option>
                <option value="smtp" {{ ($mail['driver'] ?? '') === 'smtp' ? 'selected' : '' }}>smtp — SMTP sunucu (production)</option>
            </select>
            <p class="text-[11.5px] text-slate-400 mt-1.5">Dev'de <code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">log</code> seç → gerçek mail gönderilmez, storage/logs/mail-*.log'a düşer. Production'da <code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">smtp</code>'ye geç.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">From Adresi</label>
                <input type="email" name="mail_from_address" value="{{ $mail['from_address'] ?? config('mail.from.address') }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">From İsim</label>
                <input type="text" name="mail_from_name" value="{{ $mail['from_name'] ?? config('mail.from.name') }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Reply-To Adresi</label>
                <input type="email" name="mail_reply_to" value="{{ $mail['reply_to'] ?? '' }}" placeholder="info@example.com"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Yanıtların geleceği adres (boş ise From kullanılır)</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Admin Inbox (bildirimler için)</label>
                <input type="email" name="mail_admin_inbox" value="{{ $mail['admin_inbox'] ?? '' }}" placeholder="admin@example.com"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">İletişim formu/randevu vs. yeni kayıt geldiğinde bildirilecek</p>
            </div>
        </div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 pt-2">SMTP (driver=smtp ise)</h4>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Host</label>
                <input type="text" name="mail_smtp_host" value="{{ $mail['smtp_host'] ?? '' }}" placeholder="smtp.example.com"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Port</label>
                <input type="number" name="mail_smtp_port" value="{{ $mail['smtp_port'] ?? 587 }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Kullanıcı</label>
                <input type="text" name="mail_smtp_user" value="{{ $mail['smtp_user'] ?? '' }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Şifre</label>
                <input type="password" name="mail_smtp_pass" value="{{ $mail['smtp_pass'] ?? '' }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Şifreleme</label>
            <select name="mail_smtp_encryption" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <option value=""    {{ ($mail['smtp_encryption'] ?? '') === ''    ? 'selected' : '' }}>Yok</option>
                <option value="tls" {{ ($mail['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ ($mail['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
            </select>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100"><i class="fa-solid fa-bug text-red-500 mr-1"></i> Hata Raporu Maili</h4>
        <p class="text-[12.5px] text-slate-500 -mt-1 mb-1">Exception sayfasında "Hatayı Raporla" butonuna basıldığında otomatik mail gönderilir.</p>

        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 transition">
            <input type="checkbox" name="mail_error_reporter_enabled" value="1" {{ ($mail['error_reporter_enabled'] ?? '1') ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer mt-0.5">
            <div>
                <div class="text-sm font-semibold text-slate-900">Hata raporu mailini aktif et</div>
                <div class="text-[12px] text-slate-500 mt-0.5">DB'ye her zaman yazılır; mail sadece bu açıkken gönderilir.</div>
            </div>
        </label>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Rapor Maili Alıcı</label>
                <input type="email" name="mail_error_reporter_to" value="{{ $mail['error_reporter_to'] ?? '' }}" placeholder="dev@example.com"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Boş ise Admin Inbox kullanılır.</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Aynı Hatadan Günde Max</label>
                <input type="number" name="mail_error_reporter_max_per_day" value="{{ $mail['error_reporter_max_per_day'] ?? 5 }}" min="1" max="100"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Spam'i önlemek için throttle. Yeni hatalar her zaman gönderilir.</p>
            </div>
        </div>

    @elseif($tab === 'ai')
        @php
            $groqKey   = (string) \App\Core\Config::get('services.groq.api_key', '');
            $groqModel = (string) \App\Core\Config::get('services.groq.model', 'llama-3.3-70b-versatile');
            $masked    = $groqKey === '' ? '' : substr($groqKey, 0, 7) . str_repeat('•', max(0, strlen($groqKey) - 11)) . substr($groqKey, -4);
        @endphp
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">AI · Groq Entegrasyonu</h3>

        @if($groqKey !== '')
            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-md mb-5">
                <div class="w-9 h-9 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-check"></i></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-green-900">Groq aktif</div>
                    <div class="text-[12.5px] text-green-700 font-mono truncate">{{ $masked }}</div>
                </div>
                <span class="text-[10.5px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-green-100 text-green-700">{{ $groqModel }}</span>
            </div>
        @else
            <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-md mb-5">
                <div class="w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div class="flex-1">
                    <div class="text-sm font-bold text-amber-900">Groq API key tanımlı değil</div>
                    <div class="text-[12.5px] text-amber-700">Blog'daki <em>"AI ile Oluştur"</em> butonu pasif. Aşağıdaki adımları izle.</div>
                </div>
            </div>
        @endif

        <div class="bg-slate-50 border border-slate-200 rounded-lg p-5 mb-4">
            <h4 class="text-[12px] font-bold uppercase tracking-widest text-slate-500 mb-3"><i class="fa-solid fa-shield-halved mr-1"></i> Neden config dosyası?</h4>
            <p class="text-[13px] text-slate-700 leading-relaxed">
                API key gibi hassas ayarlar DB yerine <code class="bg-white px-1.5 py-0.5 rounded text-brand-700 font-mono">config/services.php</code> dosyasında tutulur.
                Bu sayede DB yedeklerinde sızmaz, versiyon kontrolünden <code class="bg-white px-1.5 py-0.5 rounded text-brand-700 font-mono">.gitignore</code> ile çıkarılabilir.
            </p>
        </div>

        <div>
            <h4 class="text-[12px] font-bold uppercase tracking-widest text-slate-500 mb-2">Kurulum</h4>
            <ol class="space-y-2.5 text-[13.5px] text-slate-700 list-decimal pl-5 mb-4">
                <li><a href="https://console.groq.com/keys" target="_blank" rel="noopener" class="text-brand-700 hover:text-brand-900 font-semibold underline">console.groq.com/keys</a>'ten ücretsiz API key oluştur.</li>
                <li><code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">config/services.php</code> dosyasını aç.</li>
                <li><code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">groq.api_key</code> alanına anahtarını yapıştır, kaydet.</li>
                <li>İstersen <code class="bg-slate-100 px-1.5 py-0.5 rounded text-brand-700 font-mono">groq.model</code> alanından farklı model seç.</li>
            </ol>
        </div>

        <div class="bg-slate-950 rounded-lg p-4 font-mono text-[12.5px] text-slate-100 overflow-x-auto">
<span class="text-slate-500">// config/services.php</span>
return [
    'groq' =&gt; [
        'api_key' =&gt; <span class="text-emerald-300">'gsk_xxxxxxxxxxxxxxxxxxxxx'</span>,
        'model'   =&gt; <span class="text-emerald-300">'llama-3.3-70b-versatile'</span>,
    ],
];
        </div>

        <p class="text-[11.5px] text-slate-400 mt-4">Bu sayfada form yok — düzenleme sonrası sayfayı yenile, durum güncellenir.</p>

    @elseif($tab === 'seo')
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">SEO Yönetimi</h3>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Meta Etiketleri</h4>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Meta Title <span class="text-slate-400 font-normal">(max 70 karakter)</span></label>
            <input type="text" name="seo_meta_title" value="{{ $seo['meta_title'] ?? '' }}" maxlength="70"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Meta Description <span class="text-slate-400 font-normal">(max 160 karakter)</span></label>
            <textarea name="seo_meta_description" rows="2" maxlength="160"
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $seo['meta_description'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Meta Keywords (virgülle)</label>
            <input type="text" name="seo_meta_keywords" value="{{ $seo['meta_keywords'] ?? '' }}"
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">OG Görseli (URL)</label>
                <input type="text" name="seo_og_image" value="{{ $seo['og_image'] ?? '' }}" placeholder="/assets/img/og.png"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Canonical Base URL</label>
                <input type="url" name="seo_canonical_base" value="{{ $seo['canonical_base'] ?? '' }}" placeholder="https://example.com"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Analytics & Tracking</h4>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Google Analytics 4</label>
                <input type="text" name="seo_ga4_id" value="{{ $seo['ga4_id'] ?? '' }}" placeholder="G-XXXXXXXXXX"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Google Tag Manager</label>
                <input type="text" name="seo_gtm_id" value="{{ $seo['gtm_id'] ?? '' }}" placeholder="GTM-XXXXXXX"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Facebook Pixel ID</label>
                <input type="text" name="seo_fb_pixel_id" value="{{ $seo['fb_pixel_id'] ?? '' }}" placeholder="1234567890"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Search Console Doğrulama</h4>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Google Site Verification</label>
                <input type="text" name="seo_google_verification" value="{{ $seo['google_verification'] ?? '' }}" placeholder="abc123...xyz"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">meta tag içeriği (content="...")</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Bing Site Verification</label>
                <input type="text" name="seo_bing_verification" value="{{ $seo['bing_verification'] ?? '' }}" placeholder="ABC123..."
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] font-mono focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Indexleme</h4>
        <label class="inline-flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="seo_sitemap_enabled" value="1" {{ !empty($seo['sitemap_enabled']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer">
            <span class="text-sm font-medium text-slate-900">Sitemap.xml otomatik oluştur</span>
        </label>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">robots.txt İçeriği</label>
            <textarea name="seo_robots" rows="4"
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y font-mono">{{ $seo['robots'] ?? "User-agent: *\nAllow: /" }}</textarea>
        </div>

    @elseif($tab === 'security')
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3">Güvenlik Politikaları</h3>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Rate Limit & Brute Force</h4>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Form Gönderim Limiti <span class="text-slate-400 font-normal">/ saat / IP</span></label>
                <input type="number" name="security_rate_limit_per_hour" value="{{ $security['rate_limit_per_hour'] ?? 5 }}" min="1" max="100"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">İletişim/randevu formları için</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Max Login Denemesi</label>
                <input type="number" name="security_max_login_attempts" value="{{ $security['max_login_attempts'] ?? 5 }}" min="3" max="20"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Bu sayıyı aşan IP geçici banlanır</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Lockout Süresi <span class="text-slate-400 font-normal">(dakika)</span></label>
                <input type="number" name="security_lockout_minutes" value="{{ $security['lockout_minutes'] ?? 15 }}" min="1" max="1440"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Oturum & CSRF</h4>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Oturum Ömrü <span class="text-slate-400 font-normal">(dakika)</span></label>
                <input type="number" name="security_session_lifetime_min" value="{{ $security['session_lifetime_min'] ?? 120 }}" min="5" max="10080"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                <p class="text-[11.5px] text-slate-400 mt-1">Bu süre sonunda kullanıcı çıkış yapar</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">CSRF Token Ömrü <span class="text-slate-400 font-normal">(dakika)</span></label>
                <input type="number" name="security_csrf_lifetime_min" value="{{ $security['csrf_lifetime_min'] ?? 60 }}" min="5" max="240"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
            </div>
        </div>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Anahtarlar</h4>
        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 transition">
            <input type="checkbox" name="security_honeypot_enabled" value="1" {{ !empty($security['honeypot_enabled']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer mt-0.5">
            <div>
                <div class="text-sm font-semibold text-slate-900">Honeypot bot koruması</div>
                <div class="text-[12px] text-slate-500 mt-0.5">Formlara gizli bir alan ekler; bot doldurursa istek reddedilir.</div>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 transition">
            <input type="checkbox" name="security_force_https" value="1" {{ !empty($security['force_https']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer mt-0.5">
            <div>
                <div class="text-sm font-semibold text-slate-900">HTTPS'i zorunlu kıl</div>
                <div class="text-[12px] text-slate-500 mt-0.5">HTTP isteklerini 301 ile HTTPS'e yönlendirir (production için).</div>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50 transition">
            <input type="checkbox" name="security_allow_registration" value="1" {{ !empty($security['allow_registration']) ? 'checked' : '' }} class="w-4 h-4 accent-brand-700 cursor-pointer mt-0.5">
            <div>
                <div class="text-sm font-semibold text-slate-900">Halka açık kayıt</div>
                <div class="text-[12px] text-slate-500 mt-0.5">Kapalıysa kullanıcılar sadece admin tarafından oluşturulabilir.</div>
            </div>
        </label>

        <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 pt-3 mt-3 border-t border-slate-100">Engelli IP'ler</h4>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Blocklist (her satıra bir IP veya CIDR)</label>
            <textarea name="security_blocked_ips" rows="4" placeholder="192.168.1.10&#10;10.0.0.0/8&#10;203.0.113.0/24"
                      class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[13px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y font-mono">{{ $security['blocked_ips'] ?? '' }}</textarea>
            <p class="text-[11.5px] text-slate-400 mt-1.5">Listedeki IP'ler 403 alır. CIDR notasyonu desteklenir (örn: <code class="bg-slate-100 px-1 rounded">10.0.0.0/8</code>).</p>
        </div>
    @endif

    @if($tab !== 'ai')
        <div class="pt-2 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-md shadow-sm transition">
                <i class="fa-solid fa-check"></i> Kaydet
            </button>
        </div>
    @endif
</form>
@endsection
