<!doctype html>
<html lang="{{ config('app.locale', 'tr') }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ $csrf_token ?? '' }}">
    <title>@yield('title') · {{ config('app.name', 'IEF Framework') }}</title>

    {{-- ─── Fonts (Poppins) ─────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ─── Font Awesome 6 ──────────────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    {{-- ─── animate.css ─────────────────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">

    {{-- ─── Tailwind CSS (CDN, prod'da build'e geçilebilir) ─────── --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'system-ui', '-apple-system', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',
                            400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',
                            800:'#1e40af',900:'#1e3a8a',950:'#172554',
                        },
                        accent: { 400:'#22d3ee', 500:'#06b6d4', 600:'#0891b2' },
                    },
                    boxShadow: {
                        'soft': '0 2px 8px -2px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04)',
                        'glow': '0 10px 40px -10px rgba(30,64,175,.35)',
                    },
                    animation: {
                        'fade-up': 'fadeUp .5s ease forwards',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeUp: { '0%':{opacity:0,transform:'translateY(10px)'}, '100%':{opacity:1,transform:'translateY(0)'} },
                        pulseSoft: { '0%,100%':{boxShadow:'0 0 0 3px rgba(22,163,74,.15)'}, '50%':{boxShadow:'0 0 0 6px rgba(22,163,74,.05)'} },
                    }
                }
            }
        };
    </script>

    <style>
        body { font-family: 'Poppins', system-ui, sans-serif; }
        ::selection { background: rgba(30,64,175,.15); color:#1e3a8a; }
        /* Native <select> dropdown: macOS/Chrome dark mode override */
        select { color-scheme: light; }
        select option { background: #ffffff !important; color: #0f172a !important; padding: 6px 10px; }
        select option:hover, select option:checked { background: #eff6ff !important; color: #1e40af !important; }
    </style>

    {{-- ─── PWA ───────────────────────────────────────────────── --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">

    {{-- ─── SEO meta (Settings) ───────────────────────────────── --}}
    @php
        $seoTitle = \App\Models\Setting::get('seo.meta_title');
        $seoDesc  = \App\Models\Setting::get('seo.meta_description');
        $seoKw    = \App\Models\Setting::get('seo.meta_keywords');
        $seoOg    = \App\Models\Setting::get('seo.og_image');
    @endphp
    @if($seoDesc)<meta name="description" content="{{ e($seoDesc) }}">@endif
    @if($seoKw)<meta name="keywords" content="{{ e($seoKw) }}">@endif
    @if($seoOg)
        <meta property="og:image" content="{{ e($seoOg) }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    {{-- ─── Google Analytics 4 (Settings) ─────────────────────── --}}
    @php $ga4 = \App\Models\Setting::get('seo.ga4_id'); @endphp
    @if($ga4)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $ga4 }}');
        </script>
    @endif

    @yield('head')
</head>
@php
    $isEditing        = \App\Core\SiteContent::isEditing();
    $isPageEditable   = \App\Core\SiteContent::isPageEditable();
    $_currentPath     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $_normalizedPath  = \App\Core\SiteContent::normalizePath($_currentPath);
    $_autoOverrides   = \App\Core\SiteContent::getAutoOverrides($_currentPath);
    $_appearance      = $isEditing ? \App\Models\Setting::group('appearance') : [];
@endphp
@if($isEditing)
    <link rel="stylesheet" href="/assets/css/editor.css?v={{ @filemtime(ROOT_PATH . '/public/assets/css/editor.css') ?: '5' }}">
@endif
<body class="bg-white text-slate-900 antialiased min-h-screen flex flex-col {{ $isEditing ? 'osk-editing' : '' }}">

    {{-- Auto-override window vars (her ziyaretçi için, edit modu olmasa da) --}}
    <script>
        <?php
            echo "window.OSK_PAGE_EDITABLE = " . ($isPageEditable ? 'true' : 'false') . ";\n        ";
            echo "window.OSK_PAGE_PATH     = " . json_encode($_normalizedPath, JSON_UNESCAPED_SLASHES) . ";\n        ";
            echo "window.OSK_AUTO_OVERRIDES = " . json_encode((object) $_autoOverrides, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ";\n        ";
            echo "window.OSK_IS_EDITING    = " . ($isEditing ? 'true' : 'false') . ";\n        ";
            if ($isEditing) {
                echo "window.OSK_APPEARANCE  = " . json_encode((object) $_appearance, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ";\n";
            }
        ?>
        (function(){
            function pathOf(el) {
                const segs = [];
                while (el && el.nodeType === 1 && el.tagName.toLowerCase() !== 'main') {
                    const parent = el.parentElement;
                    if (!parent) break;
                    const tag = el.tagName.toLowerCase();
                    const siblings = Array.from(parent.children).filter(s => s.tagName === el.tagName);
                    const idx = siblings.indexOf(el);
                    segs.unshift(`${tag}[${idx}]`);
                    el = parent;
                }
                return segs.join('/');
            }
            const INLINE_TAGS = new Set(['span','em','strong','b','i','u','small','sub','sup','br','a','code','mark']);
            function isInlineOnly(el) {
                for (const c of el.children) {
                    if (!INLINE_TAGS.has(c.tagName.toLowerCase())) return false;
                    if (!isInlineOnly(c)) return false;
                }
                return true;
            }
            function applyOverrides() {
                const main = document.querySelector('main');
                if (!main || !window.OSK_AUTO_OVERRIDES) return;
                const ov = window.OSK_AUTO_OVERRIDES;
                main.querySelectorAll('h1,h2,h3,h4,h5,h6,p,li,blockquote,figcaption,button').forEach(el => {
                    if (!isInlineOnly(el)) return;
                    const key = pathOf(el);
                    if (!ov.hasOwnProperty(key)) return;
                    if (el.children.length > 0) el.innerHTML = ov[key];
                    else el.textContent = ov[key];
                });
                main.querySelectorAll('img').forEach(img => {
                    const key = pathOf(img);
                    if (ov.hasOwnProperty(key)) img.src = ov[key];
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyOverrides);
            else applyOverrides();
        })();
    </script>

    {{-- ═══ Nav ═══════════════════════════════════════════════════════ --}}
    <nav id="navTop" class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-200/60 transition-all">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 font-bold text-slate-900 hover:text-slate-900 group">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-900 via-brand-600 to-accent-500 text-white font-extrabold text-sm flex items-center justify-center shadow-glow group-hover:scale-105 transition-transform">i</span>
                <span class="text-[15px] tracking-tight">ief<span class="text-slate-400 font-medium">-framework</span></span>
                <span class="ml-1 text-[10px] font-bold text-brand-700 bg-brand-50 border border-brand-100 px-2 py-0.5 rounded-full tracking-wide">v{{ config('app.version','2.0.0') }}</span>
            </a>

            <div class="flex items-center gap-1">
                <a href="/"         class="hidden md:inline-flex px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition">Anasayfa</a>
                <a href="/blog"     class="hidden md:inline-flex px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition">Blog</a>
                <a href="/iletisim" class="hidden md:inline-flex px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition">İletişim</a>
                <a href="/docs"     class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition">
                    <i class="fa-solid fa-book-open text-[12px]"></i> Docs
                </a>
                <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="hidden md:inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition">
                    <i class="fa-brands fa-github text-[15px]"></i> GitHub
                </a>
                <span class="hidden md:inline-block w-px h-5 bg-slate-200 mx-2"></span>
                @if(\App\Core\Auth::check())
                    @php
                        $_navRole = \App\Core\Auth::user()?->role ?? '';
                        $_canEdit = !$isEditing && in_array($_navRole, ['superadmin','admin','editor'], true);
                    @endphp
                    @if($_canEdit)
                        <a href="/site-editor" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-amber-500 hover:from-red-700 hover:to-amber-600 rounded-full shadow-sm hover:shadow transition-all">
                            <i class="fa-solid fa-pen-to-square"></i> Düzenle
                        </a>
                    @endif
                    <a href="/admin"  class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md transition">Yönetim</a>
                    <a href="/logout" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md transition">Çıkış</a>
                @else
                    <a href="/login" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-black rounded-md shadow-sm hover:shadow transition-all">
                        Giriş Yap <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </a>
                @endif
            </div>
        </div>
    </nav>
    <script>
        (function(){
            const n=document.getElementById('navTop'); if(!n) return;
            const onScroll=()=>n.classList.toggle('shadow-soft', window.scrollY>10);
            window.addEventListener('scroll', onScroll, {passive:true}); onScroll();
        })();
    </script>

    {{-- ═══ Main ══════════════════════════════════════════════════════ --}}
    <main class="flex-1">
        @if(flash('success'))
            <div class="max-w-3xl mx-auto px-6 pt-6">
                <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm animate__animated animate__fadeInDown">
                    <i class="fa-solid fa-check-circle mt-0.5"></i><span>{{ flash('success') }}</span>
                </div>
            </div>
        @endif
        @if(flash('error'))
            <div class="max-w-3xl mx-auto px-6 pt-6">
                <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm animate__animated animate__fadeInDown">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i><span>{{ flash('error') }}</span>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- ═══ Footer ════════════════════════════════════════════════════ --}}
    <footer class="bg-slate-950 text-slate-400 mt-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 grid md:grid-cols-4 gap-10">
            <div class="md:col-span-2">
                <a href="/" class="inline-flex items-center gap-2.5 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-700 via-brand-500 to-accent-500 text-white font-extrabold text-sm flex items-center justify-center">i</span>
                    <span class="text-white font-bold text-base">ief<span class="text-slate-500 font-medium">-framework</span></span>
                </a>
                <p class="text-sm leading-relaxed max-w-md">Modern PHP uygulamaları için sade, hızlı ve sıfır konfigürasyonlu framework. .env yok — sadece çalışan kod.</p>
                <div class="flex gap-2 mt-5">
                    <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:text-white text-slate-400 flex items-center justify-center transition">
                        <i class="fa-brands fa-github"></i>
                    </a>
                    <a href="https://iefsoftware.tr" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:text-white text-slate-400 flex items-center justify-center transition">
                        <i class="fa-solid fa-globe"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-4">Belgeler</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/#quickstart" class="hover:text-white transition">Hızlı Başlangıç</a></li>
                    <li><a href="https://github.com/byk1lla/IEFFrameWork#readme" target="_blank" rel="noopener" class="hover:text-white transition">README</a></li>
                    <li><a href="https://github.com/byk1lla/IEFFrameWork/issues" target="_blank" rel="noopener" class="hover:text-white transition">Issues</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-4">Şirket</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="https://iefsoftware.tr" target="_blank" rel="noopener" class="hover:text-white transition">IEF Software</a></li>
                    <li><a href="/iletisim" class="hover:text-white transition">İletişim</a></li>
                    <li><a href="/blog" class="hover:text-white transition">Blog</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/5">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <div>© {{ date('Y') }} <a href="https://iefsoftware.tr" target="_blank" rel="noopener" class="text-slate-400 hover:text-white">IEF Software</a> · MIT · v{{ config('app.version','2.0.0') }}</div>
                <div class="inline-flex gap-1 bg-white/5 border border-white/10 rounded-md p-0.5">
                    <a href="/lang/tr" class="px-2.5 py-1 text-[10px] font-bold tracking-widest uppercase rounded {{ config('app.locale')==='tr' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">TR</a>
                    <a href="/lang/en" class="px-2.5 py-1 text-[10px] font-bold tracking-widest uppercase rounded {{ config('app.locale')==='en' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">EN</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ═══ WhatsApp floating button (Settings) ═══════════════════════ --}}
    @php $wp = \App\Models\Setting::get('general.whatsapp_number'); @endphp
    @if($wp)
        @php $wpDigits = preg_replace('/[^0-9]/', '', $wp); @endphp
        <a href="https://wa.me/{{ $wpDigits }}" target="_blank" rel="noopener"
           class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-[#25d366] hover:bg-[#1da851] text-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 group"
           title="WhatsApp ile yaz">
            <i class="fa-brands fa-whatsapp text-2xl"></i>
            <span class="absolute right-full mr-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition-all pointer-events-none">WhatsApp ile yaz</span>
        </a>
    @endif

    {{-- ═══ Scripts (Landing stack) ═══════════════════════════════════ --}}
    <script>
        // Daha önce yüklenmiş Service Worker'ı temizle (CSRF token cache sorununu önler).
        // PWA SW'i kapatıldı — manifest.webmanifest yine yüklü, install edilebilir.
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(rs => rs.forEach(r => r.unregister()));
        }
    </script>
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    @yield('scripts')
    @if($isEditing)
        <script src="/assets/js/editor.js?v={{ @filemtime(ROOT_PATH . '/public/assets/js/editor.js') ?: '5' }}"></script>
    @endif
</body>
</html>
