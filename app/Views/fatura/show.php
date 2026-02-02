<!-- Fatura Detay - ASYNC Loading -->
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

        .invoice-paper {
            background: white;
            color: black;
            min-height: 297mm;
            /* A4 height */
            padding: 20mm;
            margin: 0 auto;
            border-radius: 4px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
    </style>

    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden">
        <div>
            <a href="/fatura" class="text-gray-400 hover:text-white mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Listeye Dön
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                Fatura Detayı
                <span id="header-status"
                    class="text-xs px-2 py-1 rounded bg-gray-700 text-gray-300">Yükleniyor...</span>
            </h1>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg transition">
                <i class="fas fa-print mr-2"></i> Yazdır
            </button>
            <button id="btn-download" disabled
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition">
                <i class="fas fa-file-pdf mr-2"></i> PDF İndir
            </button>
            <button id="btn-mail" disabled
                class="px-4 py-2 bg-purple-500 hover:bg-purple-600 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition">
                <i class="fas fa-envelope mr-2"></i> E-Posta Gönder
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="flex flex-col items-center justify-center py-20 text-gray-500 print:hidden">
        <div class="ring-spinner mb-4"></div>
        <p>Fatura detayları EDM sisteminden çekiliyor...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden bg-red-500/20 text-red-400 p-8 rounded-2xl text-center print:hidden">
        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
        <h3 class="text-lg font-bold mb-1">Fatura Yüklenemedi</h3>
        <p id="error-message">Bağlantı hatası oluştu.</p>
        <button onclick="location.reload()"
            class="mt-4 px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
            Tekrar Dene
        </button>
    </div>

    <!-- Invoice Content (A4 Paper Style) -->
    <div id="invoice-content" class="hidden">
        <!-- Will be filled by JS -->
    </div>
</div>

<script>
    const INVOICE_UUID = '<?= $id ?>'; // Controller passes ID/UUID here

    document.addEventListener('DOMContentLoaded', loadInvoiceDetails);

    function loadInvoiceDetails() {
        fetch(`/api/fatura/detay/${INVOICE_UUID}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loading-state').classList.add('hidden');

                if (data.success) {
                    renderInvoice(data.data);
                    document.getElementById('invoice-content').classList.remove('hidden');

                    // Enable buttons
                    document.getElementById('btn-download').disabled = false;
                    document.getElementById('btn-mail').disabled = false;

                    // Update header status
                    const status = data.data.STATUS || 'UNKNOWN';
                    const statusEl = document.getElementById('header-status');
                    statusEl.textContent = status;
                    statusEl.className = status.includes('SUCCEED')
                        ? 'text-xs px-2 py-1 rounded bg-green-500/20 text-green-400'
                        : 'text-xs px-2 py-1 rounded bg-yellow-500/20 text-yellow-400';
                } else {
                    document.getElementById('error-state').classList.remove('hidden');
                    document.getElementById('error-message').textContent = data.error || 'Fatura bulunamadı.';
                }
            })
            .catch(err => {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('error-state').classList.remove('hidden');
                console.error(err);
            });
    }

    function renderInvoice(inv) {
        const formatMoney = (amount) => {
            return parseFloat(amount).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺';
        };

        // Construct HTML for the invoice paper
        const html = `
        <div class="invoice-paper text-sm">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8 pb-8 border-b border-gray-200">
                <div>
                   <h2 class="text-2xl font-bold mb-2">E-FATURA</h2>
                   <p class="text-gray-500">SENARYO: ${inv.PROFILEID || 'TEMELFATURA'}</p>
                   <p class="text-gray-500">TİP: ${inv.INVOICE_TYPE || 'SATIS'}</p>
                </div>
                <div class="text-right">
                    <img src="https://gib.gov.tr/sites/default/files/file_manager/images/gib_logo.png" style="height: 60px; filter: grayscale(100%); opacity: 0.5;">
                </div>
            </div>
            
            <!-- Parties -->
            <div class="flex justify-between gap-8 mb-8">
                <div class="w-1/2">
                    <h3 class="font-bold border-b border-gray-300 pb-1 mb-2">GÖNDEREN (SATICI)</h3>
                    <p class="font-bold text-lg">${inv.SENDER?.alias || '-'}</p>
                    <p>VKN: ${inv.SENDER?.vkn || '-'}</p>
                    <p class="mt-2 text-gray-600 text-xs">${inv.SENDER?._ || ''}</p>
                </div>
                <div class="w-1/2">
                    <h3 class="font-bold border-b border-gray-300 pb-1 mb-2">ALICI (MÜŞTERİ)</h3>
                    <p class="font-bold text-lg">${inv.RECEIVER?.alias || '-'}</p>
                    <p>VKN: ${inv.RECEIVER?.vkn || '-'}</p>
                    <p class="mt-2 text-gray-600 text-xs">${inv.RECEIVER?._ || ''}</p>
                </div>
            </div>
            
            <!-- Info Grid -->
            <div class="flex gap-4 mb-8">
                <div class="flex-1 bg-gray-50 p-3 rounded border border-gray-200">
                    <p class="text-xs text-gray-500">Fatura No</p>
                    <p class="font-bold">${inv.ID || '-'}</p>
                </div>
                <div class="flex-1 bg-gray-50 p-3 rounded border border-gray-200">
                    <p class="text-xs text-gray-500">Fatura Tarihi</p>
                    <p class="font-bold">${inv.ISSUE_DATE || '-'}</p>
                </div>
                <div class="flex-1 bg-gray-50 p-3 rounded border border-gray-200">
                    <p class="text-xs text-gray-500">ETTN</p>
                    <p class="font-bold text-xs truncate" title="${inv.UUID}">${inv.UUID || '-'}</p>
                </div>
            </div>
            
            <!-- Lines -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="border-b-2 border-gray-800 text-left">
                        <th class="py-2">Hizmet / Ürün</th>
                        <th class="py-2 text-right">Miktar</th>
                        <th class="py-2 text-right">Birim Fiyat</th>
                        <th class="py-2 text-right">Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Lines content would go here if we had item details -->
                     <tr>
                        <td class="py-4 border-b border-gray-200" colspan="4">
                            <i class="text-gray-500">Detaylı kalem bilgisi için XML içeriği gereklidir. Özet görüntüleniyor.</i>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Totals -->
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between py-1">
                         <span>Ödenecek Tutar:</span>
                         <span class="font-bold text-xl">${inv.PAYABLE_AMOUNT || '0.00 TL'}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 text-xs text-gray-400 text-center border-t border-gray-200 pt-4">
                Bu fatura E-Fatura Pro sistemi üzerinden oluşturulmuştur.<br>
                Maliye Bakanlığı standartlarına uygundur.
            </div>
        </div>
    `;

        document.getElementById('invoice-content').innerHTML = html;
    }
</script>