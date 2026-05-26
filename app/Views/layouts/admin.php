<!doctype html>
<html lang="{{ config('app.locale','tr') }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ $csrf_token ?? '' }}">
    <title>@yield('title') · {{ config('app.name','IEF Framework') }} Yönetim</title>

    {{-- ─── Fonts (Poppins) ─────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ─── CSS Libs ────────────────────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

    {{-- ─── Tailwind CSS ───────────────────────────────────────── --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'system-ui', 'sans-serif'] },
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
                        'card': '0 4px 16px -4px rgba(15,23,42,.08), 0 2px 4px rgba(15,23,42,.04)',
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
        select option {
            background: #ffffff !important;
            color: #0f172a !important;
            padding: 6px 10px;
            font-family: 'Poppins', sans-serif;
        }
        select option:hover, select option:checked {
            background: #eff6ff !important;
            color: #1e40af !important;
        }
        /* Sidebar dark select (Blog edit sidebar gibi) için override */
        .dark-select option {
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        /* ════════════════════════════════════════════════════════
           DataTables 2.x — modern tasarım override
           ════════════════════════════════════════════════════════ */

        /* Üst/alt kontrol satırları */
        div.dt-layout-row {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 20px;
            flex-wrap: wrap;
        }
        div.dt-layout-row:has(div.dt-info) {
            border-top: 1px solid #f1f5f9;
            background: #fafafa;
        }
        div.dt-layout-cell { display: flex; align-items: center; }
        div.dt-layout-cell.dt-start { justify-content: flex-start; }
        div.dt-layout-cell.dt-end   { justify-content: flex-end;   }
        div.dt-layout-cell.dt-full  { flex: 1; }

        /* Search input */
        div.dt-search {
            display: inline-flex !important;
            align-items: center;
            position: relative;
        }
        div.dt-search label { display: none; }
        div.dt-search input {
            padding: 8px 12px 8px 34px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #0f172a !important;
            background: #fff !important;
            min-width: 220px;
            transition: border-color .15s, box-shadow .15s;
        }
        div.dt-search::before {
            content: '\f002';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
            z-index: 1;
        }
        div.dt-search input:focus {
            outline: none !important;
            border-color: #1e40af !important;
            box-shadow: 0 0 0 3px rgba(30,64,175,.10) !important;
        }
        div.dt-search input::placeholder { color: #cbd5e1; }

        /* Length select */
        div.dt-length {
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }
        div.dt-length label { display: inline-flex; align-items: center; gap: 8px; }
        div.dt-length select {
            padding: 7px 28px 7px 12px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600;
            color: #0f172a !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat;
            background-position: right 10px center;
            appearance: none !important;
            -webkit-appearance: none !important;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
        }
        div.dt-length select:hover { border-color: #cbd5e1 !important; }
        div.dt-length select:focus {
            outline: none !important;
            border-color: #1e40af !important;
            box-shadow: 0 0 0 3px rgba(30,64,175,.10) !important;
        }

        /* Info */
        div.dt-info {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 500;
            padding: 0 !important;
        }

        /* Pagination */
        div.dt-paging {
            display: inline-flex !important;
            gap: 4px;
            margin: 0 !important;
        }
        div.dt-paging nav { display: inline-flex; gap: 4px; }
        button.dt-paging-button,
        div.dt-paging .dt-paging-button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #475569 !important;
            border-radius: 7px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            font-family: 'Poppins', sans-serif !important;
            cursor: pointer;
            transition: all .12s;
            box-shadow: none !important;
            outline: none !important;
        }
        button.dt-paging-button:hover:not(.disabled):not(.current),
        div.dt-paging .dt-paging-button:hover:not(.disabled):not(.current) {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        button.dt-paging-button.current,
        div.dt-paging .dt-paging-button.current {
            background: #1e40af !important;
            color: #fff !important;
            border-color: #1e40af !important;
            box-shadow: 0 2px 6px -1px rgba(30,64,175,.3) !important;
        }
        button.dt-paging-button.disabled,
        div.dt-paging .dt-paging-button.disabled {
            opacity: .4 !important;
            cursor: not-allowed !important;
            background: #fff !important;
            color: #94a3b8 !important;
        }

        /* Table */
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
            width: 100% !important;
        }
        table.dataTable thead tr { background: #fafafa; }
        table.dataTable thead th {
            font-weight: 600 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: .6px !important;
            color: #94a3b8 !important;
            padding: 12px 16px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            background: #fafafa !important;
            text-align: left;
        }
        table.dataTable thead th.dt-orderable-asc,
        table.dataTable thead th.dt-orderable-desc,
        table.dataTable thead th.dt-orderable-none {
            cursor: pointer;
        }
        table.dataTable thead th:hover { color: #475569 !important; }
        table.dataTable tbody td {
            padding: 14px 16px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            font-size: 13.5px;
            color: #0f172a;
            vertical-align: middle;
        }
        table.dataTable tbody tr:last-child td { border-bottom: none !important; }
        table.dataTable tbody tr { transition: background .12s; }
        table.dataTable tbody tr:hover td { background: #fafbff !important; }
        table.dataTable tr { border: none !important; }
        table.dataTable.no-footer { border-bottom: none !important; }

        /* Sort icon clean-up */
        table.dataTable thead .dt-column-order::before,
        table.dataTable thead .dt-column-order::after {
            font-size: 9px !important;
            opacity: .5;
        }
    </style>

    @yield('head')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex">

    @php
        $u = \App\Core\Auth::user();
        $uname = $u?->name ?? 'Kullanıcı';
        $uemail = $u?->email ?? '';
        $urole = $u?->role ?? 'user';
        $uinitial = mb_strtoupper(mb_substr($uname, 0, 1));
        $cur = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $isAct = fn(string $p) => $p === '/admin'
            ? ($cur === '/admin' || $cur === '/admin/')
            : str_starts_with($cur, $p);
    @endphp

    {{-- ═══ Sidebar ═══════════════════════════════════════════════════ --}}
    <aside class="w-64 min-w-[16rem] bg-white border-r border-slate-200 flex flex-col sticky top-0 h-screen">
        {{-- Brand --}}
        <a href="/admin" class="flex items-center gap-2.5 px-5 py-4 border-b border-slate-200">
            <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-900 via-brand-600 to-accent-500 text-white font-extrabold text-base flex items-center justify-center shadow-md">i</span>
            <div>
                <div class="font-bold text-[15px] tracking-tight text-slate-900">ief<span class="text-slate-400 font-medium">-framework</span></div>
                <div class="text-[10px] text-slate-400 font-medium tracking-widest uppercase">Yönetim · v{{ config('app.version','2.0.0') }}</div>
            </div>
        </a>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @php
                $groups = [
                    ['title'=>'Genel', 'items'=>[
                        ['label'=>'Dashboard','href'=>'/admin','icon'=>'fa-gauge-high','match'=>'/admin'],
                        ['label'=>'Trafik','href'=>'/admin/analytics','icon'=>'fa-chart-line','match'=>'/admin/analytics'],
                    ]],
                    ['title'=>'İçerik', 'items'=>[
                        ['label'=>'Blog','href'=>'/admin/blog','icon'=>'fa-newspaper','match'=>'/admin/blog'],
                        ['label'=>'Medya','href'=>'/admin/media','icon'=>'fa-photo-film','match'=>'/admin/media'],
                        ['label'=>'Site Editör','href'=>'/admin/editor','icon'=>'fa-wand-magic-sparkles','match'=>'/admin/editor'],
                    ]],
                    ['title'=>'İletişim', 'items'=>[
                        ['label'=>'Mesajlar','href'=>'/admin/messages','icon'=>'fa-envelope','match'=>'/admin/messages'],
                        ['label'=>'Randevular','href'=>'/admin/appointments','icon'=>'fa-calendar-check','match'=>'/admin/appointments'],
                    ]],
                    ['title'=>'Sistem', 'items'=>[
                        ['label'=>'Kullanıcılar','href'=>'/admin/users','icon'=>'fa-users','match'=>'/admin/users'],
                        ['label'=>'Ayarlar','href'=>'/admin/settings','icon'=>'fa-gear','match'=>'/admin/settings'],
                        ['label'=>'Loglar','href'=>'/admin/logs','icon'=>'fa-file-lines','match'=>'/admin/logs'],
                    ]],
                ];
            @endphp
            @foreach($groups as $g)
                <div>
                    <div class="px-3 mb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $g['title'] }}</div>
                    @foreach($g['items'] as $it)
                        @php $active = $isAct($it['match']); @endphp
                        <a href="{{ $it['href'] }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-[13.5px] font-medium transition {{ $active ? 'bg-brand-50 text-brand-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid {{ $it['icon'] }} text-[13px] w-4 text-center {{ $active ? 'text-brand-700' : 'text-slate-400' }}"></i>
                            {{ $it['label'] }}
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div>
                <div class="px-3 mb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Site</div>
                <a href="/" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-md text-[13.5px] font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[12px] w-4 text-center text-slate-400"></i>
                    Siteyi Aç
                </a>
            </div>
        </nav>

        {{-- User footer --}}
        <div class="px-3 py-3 border-t border-slate-200 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white font-bold text-sm flex items-center justify-center">{{ $uinitial }}</div>
            <div class="flex-1 min-w-0">
                <div class="text-[13px] font-semibold text-slate-900 truncate">{{ $uname }}</div>
                <div class="text-[11px] text-slate-400 truncate">{{ $urole }}</div>
            </div>
            <a href="/logout" title="Çıkış" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-slate-50 rounded-md transition">
                <i class="fa-solid fa-arrow-right-from-bracket text-[13px]"></i>
            </a>
        </div>
    </aside>

    {{-- ═══ Main ══════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-30 backdrop-blur-md bg-white/85 border-b border-slate-200 h-14 flex items-center justify-between px-7">
            <div class="text-[13.5px] text-slate-500 font-medium flex items-center gap-2">
                <a href="/admin" class="hover:text-slate-900 transition">Yönetim</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <span class="text-slate-900 font-semibold">@yield('crumb', 'Dashboard')</span>
            </div>

            <div class="flex items-center gap-2">
                <a href="/" target="_blank" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-md transition" title="Siteyi Aç">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[13px]"></i>
                </a>
                <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-md transition" title="GitHub">
                    <i class="fa-brands fa-github text-[15px]"></i>
                </a>
                <div class="w-px h-5 bg-slate-200 mx-1"></div>
                <div class="flex items-center gap-2 px-2.5 py-1.5 border border-slate-200 rounded-md">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white font-bold text-[11px] flex items-center justify-center">{{ $uinitial }}</div>
                    <div class="text-[12.5px] font-semibold text-slate-900">{{ $uname }}</div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 px-7 py-8">
            @if(flash('success'))
                <div class="flex items-start gap-3 p-4 mb-5 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm animate__animated animate__fadeInDown">
                    <i class="fa-solid fa-check-circle mt-0.5"></i><span>{{ flash('success') }}</span>
                </div>
            @endif
            @if(flash('error'))
                <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm animate__animated animate__fadeInDown">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i><span>{{ flash('error') }}</span>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- ═══ Scripts (Admin stack) ═════════════════════════════════════ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script>
        // DataTables varsayılan dil (Türkçe)
        if (window.jQuery) {
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    search:'', searchPlaceholder:'Ara…',
                    lengthMenu:'_MENU_ kayıt',
                    info:'_TOTAL_ kayıt — sayfa _PAGE_/_PAGES_',
                    infoEmpty:'Kayıt yok',
                    zeroRecords:'Sonuç bulunamadı',
                    emptyTable:'Henüz veri yok',
                    paginate:{ first:'«', last:'»', next:'›', previous:'‹' },
                },
                pageLength:25,
                lengthMenu:[10,25,50,100],
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
