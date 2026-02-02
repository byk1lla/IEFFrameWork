<!DOCTYPE html>
<html lang="tr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>
        <?= $title ?? 'E-Fatura Pro' ?>
    </title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }

        /* Mobile-first base styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }

        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* HTMX loading indicator */
        .htmx-request .htmx-indicator {
            display: inline-flex;
        }

        .htmx-indicator {
            display: none;
        }

        /* Mobile menu animation */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.2);
        }

        /* PWA Safe area */
        .safe-top {
            padding-top: env(safe-area-inset-top);
        }

        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
</head>

<body class="text-gray-100 antialiased">
    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-50 glass safe-top">
        <div class="flex items-center justify-between px-4 py-3">
            <button id="menuToggle" class="text-2xl">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-lg font-semibold text-primary-400">
                <i class="fas fa-file-invoice"></i> E-Fatura Pro
            </h1>
            <button class="relative">
                <i class="fas fa-bell text-xl"></i>
                <span
                    class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs flex items-center justify-center">3</span>
            </button>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="mobile-menu lg:translate-x-0 fixed top-0 left-0 h-full w-72 glass z-50 safe-top">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-8">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">E-Fatura Pro</h1>
                    <p class="text-xs text-gray-400">v2.0 MVC</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="/"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary-500/20 text-primary-400 font-medium">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/fatura" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
                    <i class="fas fa-file-invoice w-5"></i>
                    <span>Faturalar</span>
                </a>
                <a href="/fatura/yeni" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
                    <i class="fas fa-plus-circle w-5"></i>
                    <span>Yeni Fatura</span>
                </a>
                <a href="/cari" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
                    <i class="fas fa-users w-5"></i>
                    <span>Cariler</span>
                </a>
                <a href="/raporlar" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span>Raporlar</span>
                </a>

                <div class="border-t border-white/10 my-4"></div>

                <a href="/ayarlar" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
                    <i class="fas fa-cog w-5"></i>
                    <span>Ayarlar</span>
                </a>
            </nav>
        </div>

        <!-- User Profile -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 safe-bottom">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center font-bold">
                    DE
                </div>
                <div class="flex-1">
                    <p class="font-medium text-sm">Dursun Erdoğdu</p>
                    <p class="text-xs text-gray-400">Admin</p>
                </div>
                <a href="/logout" class="text-gray-400 hover:text-red-400 transition">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleMenu()"></div>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen pt-16 lg:pt-0">
        <div class="p-4 lg:p-8">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 glass safe-bottom z-40">
        <div class="flex justify-around py-3">
            <a href="/" class="flex flex-col items-center text-primary-400">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs mt-1">Ana Sayfa</span>
            </a>
            <a href="/fatura" class="flex flex-col items-center text-gray-400 hover:text-primary-400 transition">
                <i class="fas fa-file-invoice text-xl"></i>
                <span class="text-xs mt-1">Faturalar</span>
            </a>
            <a href="/fatura/yeni" class="flex flex-col items-center">
                <div
                    class="w-14 h-14 -mt-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-lg shadow-primary-500/50">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
            </a>
            <a href="/cari" class="flex flex-col items-center text-gray-400 hover:text-primary-400 transition">
                <i class="fas fa-users text-xl"></i>
                <span class="text-xs mt-1">Cariler</span>
            </a>
            <a href="/ayarlar" class="flex flex-col items-center text-gray-400 hover:text-primary-400 transition">
                <i class="fas fa-cog text-xl"></i>
                <span class="text-xs mt-1">Ayarlar</span>
            </a>
        </div>
    </nav>

    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('SW registered'))
                .catch(err => console.log('SW failed', err));
        }

        // Mobile Menu Toggle
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }

        document.getElementById('menuToggle')?.addEventListener('click', toggleMenu);

        // HTMX Extensions
        document.body.addEventListener('htmx:afterSwap', function (evt) {
            // Re-init any JS after HTMX swap
        });

        // SweetAlert Toast shortcut
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        // Dark mode toggle (if needed)
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }
    </script>
</body>

</html>