<!doctype html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · IEF Framework Docs</title>
    <meta name="robots" content="noindex">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    {{-- Prism syntax highlight (Tomorrow Night teması, dark code blocks) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'SF Mono', 'monospace'],
                    },
                    colors: {
                        brand: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8', 900:'#1e3a8a' },
                    },
                }
            }
        };
    </script>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        ::selection { background: rgba(37, 99, 235, .18); }

        /* Prose içerik stilleri (Tailwind Typography eşdeğeri, manuel) */
        .prose { color: #1e293b; line-height: 1.75; font-size: 16px; }
        .prose > * + * { margin-top: 1.25em; }
        .prose h1 { font-size: 2.25rem; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; margin-top: 0; }
        .prose h2 { font-size: 1.65rem; font-weight: 700; color: #0f172a; margin-top: 2.5em; padding-bottom: .3em; border-bottom: 1px solid #e2e8f0; scroll-margin-top: 96px; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-top: 2em; scroll-margin-top: 96px; }
        .prose h4 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-top: 1.75em; }
        .prose p, .prose ul, .prose ol { font-size: 15.5px; }
        .prose a { color: #2563eb; text-decoration: underline; text-decoration-color: rgba(37, 99, 235, .35); text-underline-offset: 3px; }
        .prose a:hover { color: #1d4ed8; text-decoration-color: #1d4ed8; }
        .prose ul, .prose ol { padding-left: 1.5em; }
        .prose ul { list-style: disc; }
        .prose ol { list-style: decimal; }
        .prose li + li { margin-top: .35em; }
        .prose li > p { margin: 0; }
        .prose strong { color: #0f172a; font-weight: 600; }
        .prose blockquote {
            border-left: 4px solid #2563eb;
            background: linear-gradient(135deg, #eff6ff, #dbeafe22);
            padding: 14px 18px;
            color: #1e3a8a;
            font-size: 14.5px;
            border-radius: 0 8px 8px 0;
        }
        .prose blockquote p { margin: 0; }
        .prose blockquote strong { color: #1e40af; }

        /* Inline code */
        .prose :not(pre) > code {
            background: #f1f5f9;
            color: #be185d;
            padding: 1.5px 6px;
            border-radius: 4px;
            font: 600 13px 'JetBrains Mono', monospace;
            border: 1px solid #e2e8f0;
        }

        /* Code blocks — Prism override */
        .prose pre {
            background: #0f172a !important;
            border-radius: 12px;
            padding: 18px 20px;
            overflow-x: auto;
            font: 13.5px/1.65 'JetBrains Mono', monospace !important;
            box-shadow: 0 4px 16px -4px rgba(15,23,42,.18);
            position: relative;
        }
        .prose pre code { background: none !important; padding: 0 !important; border: 0 !important; color: #e2e8f0; font-size: 13.5px !important; }

        /* Tables */
        .prose table { width: 100%; font-size: 14px; border-collapse: collapse; }
        .prose th, .prose td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .prose th { background: #f8fafc; font-weight: 700; color: #475569; font-size: 12.5px; text-transform: uppercase; letter-spacing: .05em; }
        .prose tr:hover td { background: #f8fafc; }

        /* HR */
        .prose hr { margin: 3em 0; border: 0; border-top: 1px solid #e2e8f0; }

        /* Sidebar */
        .docs-nav a.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            border-left-color: #2563eb;
        }
        .docs-nav details > summary { list-style: none; cursor: pointer; }
        .docs-nav details > summary::-webkit-details-marker { display: none; }
        .docs-nav details[open] > summary i.fa-chevron-down { transform: rotate(0deg); }
        .docs-nav details > summary i.fa-chevron-down { transform: rotate(-90deg); transition: transform .15s; }

        /* TOC scrollspy ışıltısı */
        .toc a.active { color: #1d4ed8; font-weight: 600; }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">

    {{-- ═══ Top Bar ═══════════════════════════════════════════ --}}
    <header class="sticky top-0 z-40 backdrop-blur-md bg-white/85 border-b border-slate-200/70">
        <div class="max-w-screen-2xl mx-auto px-4 lg:px-8 h-14 flex items-center justify-between gap-4">
            <a href="/" class="flex items-center gap-2.5 font-bold text-slate-900 group">
                <span class="w-7 h-7 rounded-md bg-gradient-to-br from-brand-900 via-brand-600 to-brand-500 text-white text-xs font-extrabold flex items-center justify-center shadow-sm">i</span>
                <span class="text-[14px]">ief<span class="text-slate-400 font-medium">-framework</span></span>
                <span class="ml-1 text-[10px] font-bold text-brand-700 bg-brand-50 border border-brand-100 px-1.5 py-0.5 rounded">DOCS</span>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <input type="text" id="docsSearch" placeholder="Konu ara... (⌘K)"
                       class="w-72 px-3 py-1.5 text-[13px] border border-slate-300 rounded-md focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15">
            </div>

            <div class="flex items-center gap-2">
                <a href="/" class="hidden sm:inline-flex px-3 py-1.5 text-[13px] font-medium text-slate-600 hover:text-slate-900 rounded">Site</a>
                <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[13px] font-medium text-slate-600 hover:text-slate-900 rounded">
                    <i class="fa-brands fa-github"></i> GitHub
                </a>
                <button id="navToggle" class="lg:hidden w-9 h-9 rounded-md border border-slate-300 text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- ═══ 3-Column Layout ═══════════════════════════════════ --}}
    <div class="max-w-screen-2xl mx-auto px-4 lg:px-8 grid lg:grid-cols-[260px_1fr_220px] gap-8 pt-8 pb-16">

        {{-- ─── Left Sidebar (Navigation) ────────────────────── --}}
        <aside id="docsNav" class="docs-nav lg:sticky lg:top-20 lg:self-start lg:max-h-[calc(100vh-6rem)] overflow-y-auto pr-2">
            <nav class="space-y-5">
                @foreach($nav as $section => $items)
                    <details open class="group">
                        <summary class="flex items-center justify-between mb-2 text-[11px] font-bold uppercase tracking-widest text-slate-500 hover:text-slate-900">
                            <span>{{ $section }}</span>
                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400"></i>
                        </summary>
                        <ul class="space-y-0.5">
                            @foreach($items as $slug => $label)
                                <li>
                                    <a href="/docs/{{ $slug }}"
                                       class="block px-3 py-1.5 text-[13.5px] text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md border-l-2 border-transparent transition {{ $topic === $slug ? 'active' : '' }}">
                                        {{ $label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endforeach
            </nav>
        </aside>

        {{-- ─── Main Content ─────────────────────────────────── --}}
        <main class="prose min-w-0">
            {!! $html !!}

            {{-- Footer nav --}}
            <hr>
            <div class="flex items-center justify-between text-[13px] text-slate-500 mt-8">
                <a href="https://github.com/byk1lla/IEFFrameWork/edit/main/docs/{{ $topic }}.md" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-brand-700">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i> Bu sayfayı GitHub'da düzenle
                </a>
                <span>© {{ date('Y') }} IEF Software</span>
            </div>
        </main>

        {{-- ─── Right TOC (Table of Contents) ────────────────── --}}
        <aside class="toc hidden lg:block sticky top-20 self-start max-h-[calc(100vh-6rem)] overflow-y-auto">
            @if(!empty($headings))
                <div class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-3">Bu sayfada</div>
                <ul class="space-y-1.5 text-[13px]">
                    @foreach($headings as $h)
                        <li class="{{ $h['level'] === 3 ? 'pl-3' : '' }}">
                            <a href="#{{ $h['slug'] }}" class="text-slate-500 hover:text-slate-900 transition block leading-snug">
                                {{ $h['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </div>

    {{-- Prism JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-nginx.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-apacheconf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-sql.min.js"></script>

    <script>
        // ⌘K / Ctrl+K → search focus
        document.addEventListener('keydown', e => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('docsSearch')?.focus();
            }
        });

        // Mobile nav toggle
        document.getElementById('navToggle')?.addEventListener('click', () => {
            document.getElementById('docsNav').classList.toggle('hidden');
        });

        // Search — sidebar üzerinde live filter
        const search = document.getElementById('docsSearch');
        search?.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.docs-nav details').forEach(det => {
                let anyVisible = false;
                det.querySelectorAll('li').forEach(li => {
                    const txt = li.textContent.toLowerCase();
                    const match = !q || txt.includes(q);
                    li.style.display = match ? '' : 'none';
                    if (match) anyVisible = true;
                });
                det.style.display = anyVisible ? '' : 'none';
                if (q && anyVisible) det.setAttribute('open', '');
            });
        });

        // TOC scrollspy
        const tocLinks = document.querySelectorAll('.toc a[href^="#"]');
        const headings = Array.from(tocLinks).map(a => document.getElementById(decodeURIComponent(a.getAttribute('href').slice(1)))).filter(Boolean);
        function onScroll() {
            const y = window.scrollY + 120;
            let cur = null;
            for (const h of headings) {
                if (h.offsetTop <= y) cur = h;
            }
            tocLinks.forEach(a => a.classList.toggle('active', cur && a.getAttribute('href') === '#' + cur.id));
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        // Code block "Copy" butonu
        document.querySelectorAll('pre').forEach(pre => {
            const btn = document.createElement('button');
            btn.textContent = 'Kopyala';
            btn.style.cssText = 'position:absolute;top:8px;right:8px;background:rgba(255,255,255,.08);color:#cbd5e1;border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:3px 9px;font:600 11px Inter;cursor:pointer;opacity:0;transition:opacity .15s';
            pre.style.position = 'relative';
            pre.appendChild(btn);
            pre.addEventListener('mouseenter', () => btn.style.opacity = '1');
            pre.addEventListener('mouseleave', () => btn.style.opacity = '0');
            btn.addEventListener('click', async () => {
                await navigator.clipboard.writeText(pre.querySelector('code')?.textContent || pre.textContent);
                btn.textContent = 'Kopyalandı ✓';
                setTimeout(() => btn.textContent = 'Kopyala', 1500);
            });
        });
    </script>
</body>
</html>
