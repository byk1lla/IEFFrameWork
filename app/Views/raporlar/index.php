<!-- Raporlar -->
<div class="space-y-6">
    <style>
        .ring-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(59, 130, 246, 0.2);
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Header -->
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold">📊 Raporlar</h1>
        <p class="text-gray-400 mt-1">Finansal analizler ve istatistikler</p>
    </div>

    <!-- Report Cards -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Aylık Özet -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-400 text-xl"></i>
                </div>
                <h3 class="font-semibold">Aylık Özet</h3>
            </div>
            <div id="monthly-summary" class="flex items-center justify-center py-6">
                <div class="ring-spinner"></div>
            </div>
        </div>

        <!-- Fatura Dağılımı -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-green-400 text-xl"></i>
                </div>
                <h3 class="font-semibold">Fatura Dağılımı</h3>
            </div>
            <div id="invoice-dist" class="h-48">
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>

        <!-- KDV Özeti -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-percent text-purple-400 text-xl"></i>
                </div>
                <h3 class="font-semibold">KDV Özeti</h3>
            </div>
            <div class="space-y-3 mt-4">
                <div class="flex justify-between">
                    <span class="text-gray-400">Hesaplanan KDV</span>
                    <span class="font-semibold text-green-400">₺0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">İndirilecek KDV</span>
                    <span class="font-semibold text-red-400">₺0</span>
                </div>
                <hr class="border-white/10">
                <div class="flex justify-between">
                    <span class="text-gray-400">Ödenecek KDV</span>
                    <span class="font-bold text-lg">₺0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold mb-4">
            <i class="fas fa-download text-primary-400 mr-2"></i>
            Hızlı Raporlar
        </h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button class="p-4 bg-white/5 rounded-xl hover:bg-white/10 transition text-left">
                <i class="fas fa-file-excel text-green-400 text-2xl mb-2"></i>
                <p class="font-medium">Satış Raporu</p>
                <p class="text-xs text-gray-400">Excel formatında indir</p>
            </button>
            <button class="p-4 bg-white/5 rounded-xl hover:bg-white/10 transition text-left">
                <i class="fas fa-file-pdf text-red-400 text-2xl mb-2"></i>
                <p class="font-medium">E-Fatura Listesi</p>
                <p class="text-xs text-gray-400">PDF formatında indir</p>
            </button>
            <button class="p-4 bg-white/5 rounded-xl hover:bg-white/10 transition text-left">
                <i class="fas fa-file-csv text-blue-400 text-2xl mb-2"></i>
                <p class="font-medium">Cari Hareketler</p>
                <p class="text-xs text-gray-400">CSV formatında indir</p>
            </button>
            <button class="p-4 bg-white/5 rounded-xl hover:bg-white/10 transition text-left">
                <i class="fas fa-file-invoice text-purple-400 text-2xl mb-2"></i>
                <p class="font-medium">KDV Beyanı</p>
                <p class="text-xs text-gray-400">Beyan formatı</p>
            </button>
        </div>
    </div>
</div>

<script>
    // Load monthly summary from API
    fetch('/api/dashboard/stats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const s = data.data;
                document.getElementById('monthly-summary').innerHTML = `
                <div class="text-center w-full">
                    <p class="text-3xl font-bold">₺${(s.aylık_ciro || 0).toLocaleString('tr-TR')}</p>
                    <p class="text-gray-400 text-sm mt-1">Toplam Ciro</p>
                    <div class="flex justify-around mt-4 text-sm">
                        <div>
                            <p class="font-semibold">${s.toplam_giden || 0}</p>
                            <p class="text-gray-500">Satış</p>
                        </div>
                        <div>
                            <p class="font-semibold">${s.toplam_gelen || 0}</p>
                            <p class="text-gray-500">Alış</p>
                        </div>
                    </div>
                </div>`;
            } else {
                document.getElementById('monthly-summary').innerHTML = '<p class="text-gray-500">Veri yüklenemedi</p>';
            }
        });
</script>