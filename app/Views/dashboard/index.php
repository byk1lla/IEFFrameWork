<!-- Dashboard Content - ASYNC Loading with Ring Spinner -->
<div class="space-y-6">
    <!-- Ring Spinner CSS -->
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
            to { transform: rotate(360deg); }
        }
    </style>
    
    <!-- Welcome Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold">Merhaba, <?= htmlspecialchars($authUser['name'] ?? 'Kullanıcı') ?>! 👋</h1>
            <p class="text-gray-400 mt-1">İşte güncel finansal özetin</p>
        </div>
        <a href="/fatura/yeni" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-primary-500/30 transition">
            <i class="fas fa-plus"></i>
            <span>Yeni Fatura</span>
        </a>
    </div>
    
    <!-- Stats Cards - Async Loading -->
    <div id="stats-container">
        <!-- Loading skeleton with spinner -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass rounded-2xl p-6 flex items-center justify-center h-28 animate-pulse bg-white/5">
                <div class="ring-spinner"></div>
            </div>
            <div class="glass rounded-2xl p-6 h-28 bg-white/5"></div>
            <div class="glass rounded-2xl p-6 h-28 bg-white/5"></div>
            <div class="glass rounded-2xl p-6 h-28 bg-white/5"></div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/fatura/yeni" class="glass rounded-xl p-4 text-center hover:bg-white/10 transition">
            <i class="fas fa-file-invoice text-3xl text-blue-400 mb-2"></i>
            <p class="text-sm">Fatura Oluştur</p>
        </a>
        <a href="/cari/yeni" class="glass rounded-xl p-4 text-center hover:bg-white/10 transition">
            <i class="fas fa-user-plus text-3xl text-purple-400 mb-2"></i>
            <p class="text-sm">Cari Ekle</p>
        </a>
        <a href="/fatura" class="glass rounded-xl p-4 text-center hover:bg-white/10 transition">
            <i class="fas fa-list text-3xl text-green-400 mb-2"></i>
            <p class="text-sm">Fatura Listesi</p>
        </a>
        <a href="/raporlar" class="glass rounded-xl p-4 text-center hover:bg-white/10 transition">
            <i class="fas fa-chart-pie text-3xl text-orange-400 mb-2"></i>
            <p class="text-sm">Raporlar</p>
        </a>
    </div>
    
    <!-- Recent Invoices - Async -->
    <div class="glass rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">
                <i class="fas fa-history text-primary-400 mr-2"></i>
                Son Faturalar
            </h3>
            <a href="/fatura" class="text-primary-400 hover:text-primary-300 text-sm">
                Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div id="recent-invoices">
            <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                <div class="ring-spinner mb-3"></div>
                <span>Faturalar yükleniyor...</span>
            </div>
        </div>
    </div>
</div>

<script>
// Load stats async
fetch('/api/dashboard/stats')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderStats(data.data);
        } else {
            document.getElementById('stats-container').innerHTML = `
                <div class="bg-yellow-500/20 text-yellow-400 p-4 rounded-xl flex items-center gap-3">
                    <i class="fas fa-info-circle"></i>
                    <span>EDM bağlantısı kurulamadı. Veriler yüklenemedi.</span>
                </div>`;
        }
    })
    .catch(err => {
        document.getElementById('stats-container').innerHTML = `
            <div class="bg-red-500/20 text-red-400 p-4 rounded-xl">
                <i class="fas fa-exclamation-triangle mr-2"></i>Bağlantı hatası
            </div>`;
    });

// Load recent invoices async
fetch('/api/fatura/liste?limit=5')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderRecentInvoices(data.data);
        } else {
            document.getElementById('recent-invoices').innerHTML = `
                <p class="text-gray-500 text-center py-8">Fatura bulunamadı</p>`;
        }
    })
    .catch(err => {
        document.getElementById('recent-invoices').innerHTML = `
            <p class="text-red-400 text-center py-8">Veriler yüklenemedi</p>`;
    });

function renderStats(stats) {
    document.getElementById('stats-container').innerHTML = `
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass rounded-2xl p-4 lg:p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-inbox text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold">${stats.toplam_gelen || 0}</p>
                        <p class="text-xs lg:text-sm text-gray-400">Gelen Fatura</p>
                    </div>
                </div>
            </div>
            <div class="glass rounded-2xl p-4 lg:p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-paper-plane text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold">${stats.toplam_giden || 0}</p>
                        <p class="text-xs lg:text-sm text-gray-400">Giden Fatura</p>
                    </div>
                </div>
            </div>
            <div class="glass rounded-2xl p-4 lg:p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold">${stats.onaylanan || 0}</p>
                        <p class="text-xs lg:text-sm text-gray-400">Onaylanan</p>
                    </div>
                </div>
            </div>
            <div class="glass rounded-2xl p-4 lg:p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xl lg:text-2xl font-bold">₺${(stats.aylık_ciro || 0).toLocaleString('tr-TR')}</p>
                        <p class="text-xs lg:text-sm text-gray-400">Bu Ay</p>
                    </div>
                </div>
            </div>
        </div>`;
}

function renderRecentInvoices(invoices) {
    if (!invoices || !invoices.length) {
        document.getElementById('recent-invoices').innerHTML = `
            <p class="text-gray-500 text-center py-8">Henüz fatura bulunamadı</p>`;
        return;
    }
    
    let html = '<div class="space-y-3">';
    invoices.forEach(inv => {
        const statusClass = (inv.status || '').includes('SUCCEED') ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400';
        const statusText = (inv.status || '').includes('SUCCEED') ? 'Onaylı' : 'Bekliyor';
        
        html += `
            <div class="bg-white/5 rounded-xl p-4 flex items-center justify-between hover:bg-white/10 transition cursor-pointer"
                 onclick="window.location='/fatura/${inv.uuid || ''}'">
                <div>
                    <p class="font-medium">${inv.id || '-'}</p>
                    <p class="text-sm text-gray-400">${inv.receiver || '-'}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-green-400">₺${(inv.amount || 0).toLocaleString('tr-TR', {minimumFractionDigits: 2})}</p>
                    <span class="text-xs ${statusClass} px-2 py-0.5 rounded">${statusText}</span>
                </div>
            </div>`;
    });
    html += '</div>';
    
    document.getElementById('recent-invoices').innerHTML = html;
}
</script>