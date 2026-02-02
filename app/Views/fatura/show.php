<!-- Fatura Detay - PDF Görüntüleme -->
<div class="h-[calc(100vh-140px)] flex flex-col gap-4">
    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden shrink-0">
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
            <a id="btn-download" href="#" target="_blank"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg transition text-white flex items-center">
                <i class="fas fa-external-link-alt mr-2"></i> Yeni Sekmede Aç
            </a>
        </div>
    </div>

    <!-- PDF Viewer -->
    <div class="flex-1 bg-gray-900 rounded-2xl overflow-hidden border border-white/10 relative group shadow-2xl">
        <!-- Sexy Loading Overlay -->
        <div id="pdf-loading"
            class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/90 backdrop-blur-sm z-30">
            <div class="relative w-20 h-20 mb-6">
                <div class="absolute inset-0 border-4 border-blue-500/10 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-t-blue-500 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-2xl text-blue-500 animate-pulse"></i>
                </div>
            </div>
            <div class="text-xl font-medium text-white flex items-center gap-1">
                <span>Yükleniyor</span>
                <span class="inline-flex w-8 justify-start dot-animation"></span>
            </div>
            <p class="text-gray-500 text-sm mt-2">E-Fatura sistemi hazırlanıyor...</p>
        </div>

        <iframe id="pdf-frame" src="" class="w-full h-full border-none opacity-0 transition-opacity duration-700"
            onload="hideLoading()"></iframe>
    </div>
</div>

<style>
    .dot-animation::after {
        content: '';
        animation: dots 2s infinite;
    }

    @keyframes dots {
        0% {
            content: '';
        }

        25% {
            content: '.';
        }

        50% {
            content: '..';
        }

        75% {
            content: '...';
        }

        100% {
            content: '';
        }
    }
</style>

<script>
    const INVOICE_UUID = '<?= $id ?>';

    function hideLoading() {
        const frame = document.getElementById('pdf-frame');
        const loader = document.getElementById('pdf-loading');
        frame.classList.remove('opacity-0');
        loader.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Load Status Metadata
        fetchMetadata();

        // 2. Set PDF Source
        const pdfUrl = `/api/fatura/pdf/${INVOICE_UUID}`;
        document.getElementById('pdf-frame').src = pdfUrl;
        document.getElementById('btn-download').href = pdfUrl;
    });

    function fetchMetadata() {
        fetch(`/api/fatura/detay/${INVOICE_UUID}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const status = data.data.STATUS || 'UNKNOWN';
                    const statusEl = document.getElementById('header-status');
                    statusEl.textContent = status;
                    statusEl.className = status.includes('SUCCEED')
                        ? 'text-xs px-2 py-1 rounded bg-green-500/20 text-green-400'
                        : 'text-xs px-2 py-1 rounded bg-yellow-500/20 text-yellow-400';
                }
            })
            .catch(console.error);
    }
</script>