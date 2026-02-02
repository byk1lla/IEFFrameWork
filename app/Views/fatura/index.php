<!-- Fatura Listesi - ASYNC Loading -->
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
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold">📄 Faturalar</h1>
            <p class="text-gray-400 mt-1">Gelen ve giden faturalarınız</p>
        </div>
        <a href="/fatura/yeni"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-blue-500/30 transition">
            <i class="fas fa-plus"></i>
            <span>Yeni Fatura</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="glass rounded-xl p-4 flex flex-wrap gap-4">
        <select id="filterType" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm">
            <option value="">Tüm Faturalar</option>
            <option value="SATIS">Giden (Satış)</option>
            <option value="ALIS">Gelen (Alış)</option>
        </select>
        <select id="filterDays" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm">
            <option value="30">Son 30 Gün</option>
            <option value="90">Son 90 Gün</option>
            <option value="180">Son 6 Ay</option>
        </select>
        <input type="text" id="searchInput" placeholder="Fatura No veya Firma Ara..."
            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm min-w-[200px]">
        <button onclick="loadFaturalar()" class="px-4 py-2 bg-blue-500 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <!-- Invoice List Container -->
    <div class="glass rounded-2xl overflow-hidden">
        <div id="fatura-list">
            <!-- Loading -->
            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                <div class="ring-spinner mb-3"></div>
                <span>Faturalar yükleniyor...</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Load invoices on page load
    document.addEventListener('DOMContentLoaded', loadFaturalar);

    function loadFaturalar() {
        const days = document.getElementById('filterDays').value;
        const container = document.getElementById('fatura-list');

        container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
            <div class="ring-spinner mb-3"></div>
            <span>Faturalar yükleniyor...</span>
        </div>`;

        fetch(`/api/fatura/liste?limit=100`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length) {
                    renderFaturalar(data.data);
                } else {
                    container.innerHTML = `
                    <div class="p-12 text-center">
                        <i class="fas fa-file-invoice text-6xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400">Henüz fatura bulunamadı</p>
                        <a href="/fatura/yeni" class="inline-block mt-4 text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i> İlk faturanızı oluşturun
                        </a>
                    </div>`;
                }
            })
            .catch(err => {
                container.innerHTML = `
                <div class="p-8 text-center text-red-400">
                    <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                    <p>Faturalar yüklenirken hata oluştu</p>
                </div>`;
            });
    }

    function renderFaturalar(faturalar) {
        const searchQuery = document.getElementById('searchInput').value.toLowerCase();
        const filtered = faturalar.filter(f => {
            if (!searchQuery) return true;
            return (f.id || '').toLowerCase().includes(searchQuery) ||
                (f.receiver || '').toLowerCase().includes(searchQuery);
        });

        let html = '';

        // Mobile View
        html += '<div class="lg:hidden divide-y divide-white/10">';
        filtered.forEach(f => {
            const statusClass = (f.status || '').includes('SUCCEED') ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400';
            const statusText = (f.status || '').includes('SUCCEED') ? 'Onaylı' : 'Bekliyor';

            html += `
            <div class="p-4 hover:bg-white/5 transition cursor-pointer" onclick="window.location='/fatura/${f.uuid || ''}'">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium">${f.id || '-'}</p>
                        <p class="text-sm text-gray-400 mt-1">${f.receiver || '-'}</p>
                        <p class="text-xs text-gray-500 mt-1">${f.date || '-'}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-green-400">₺${(f.amount || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}</p>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded ${statusClass}">${statusText}</span>
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';

        // Desktop Table
        html += `
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-gray-400">
                    <tr>
                        <th class="text-left py-4 px-6">Fatura No</th>
                        <th class="text-left py-4 px-6">Alıcı</th>
                        <th class="text-left py-4 px-6">Tarih</th>
                        <th class="text-left py-4 px-6">Tip</th>
                        <th class="text-right py-4 px-6">Tutar</th>
                        <th class="text-center py-4 px-6">Durum</th>
                        <th class="text-center py-4 px-6">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">`;

        filtered.forEach(f => {
            const statusClass = (f.status || '').includes('SUCCEED') ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400';
            const statusText = (f.status || '').includes('SUCCEED') ? 'Onaylı' : 'Bekliyor';

            html += `
            <tr class="hover:bg-white/5 transition">
                <td class="py-4 px-6 font-medium">${f.id || '-'}</td>
                <td class="py-4 px-6">${f.receiver || '-'}</td>
                <td class="py-4 px-6 text-gray-400">${f.date || '-'}</td>
                <td class="py-4 px-6">
                    <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded">${f.type || 'SATIS'}</span>
                </td>
                <td class="py-4 px-6 text-right font-semibold">₺${(f.amount || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}</td>
                <td class="py-4 px-6 text-center">
                    <span class="text-xs px-2 py-1 rounded ${statusClass}">${statusText}</span>
                </td>
                <td class="py-4 px-6 text-center">
                    <a href="/fatura/${f.uuid || ''}" class="text-blue-400 hover:text-blue-300 mx-1" title="Görüntüle">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="text-green-400 hover:text-green-300 mx-1" title="PDF İndir">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';

        document.getElementById('fatura-list').innerHTML = html;
    }

    // Search filter
    document.getElementById('searchInput').addEventListener('input', function () {
        // Re-render with current data (would need to cache data for this to work properly)
        // For now, just reload
    });
</script>