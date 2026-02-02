<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold">🔍 Mükellef Sorgula</h1>
        <p class="text-gray-400">Ünvan, VKN veya TCKN ile mükellef bilgilerine anında erişin.</p>
    </div>

    <!-- Search Form -->
    <div class="glass rounded-2xl p-6">
        <form action="/mukellef/sorgula" method="POST" id="searchForm">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-500"></i>
                    </div>
                    <input type="text" name="q" id="searchInput" value="<?= $query ?? $vkn ?? '' ?>" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-4 pl-12 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 transition text-lg"
                        placeholder="Ünvan veya VKN/TCKN yazın...">
                </div>

                <button type="submit" id="searchBtn"
                    class="py-4 px-8 bg-blue-600 hover:bg-blue-500 rounded-xl font-semibold transition flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
                    <span id="btnText">Sorgula</span>
                    <i class="fas fa-arrow-right" id="btnIcon"></i>
                </button>
            </div>

            <div class="flex gap-4 mt-3 px-2">
                <label class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-white transition">
                    <input type="radio" name="search_type" value="auto" checked class="accent-blue-500">
                    Otomatik Algıla
                </label>
                <label class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-white transition">
                    <input type="radio" name="search_type" value="vkn" class="accent-blue-500">
                    Sadece VKN/TCKN
                </label>
            </div>
        </form>
    </div>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div
            class="bg-red-500/20 border border-red-500/30 text-red-200 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <!-- Results Table -->
    <?php if (!empty($results)): ?>
        <div id="finalResults" class="space-y-4 animate-fade-in">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-list text-blue-400"></i>
                    Bulunan Mükellefler (<?= count($results) ?>)
                </h3>
            </div>

            <div class="glass rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-white/5 text-[10px] uppercase tracking-wider text-gray-400 font-bold border-b border-white/5">
                                <th class="px-6 py-4">Ünvan / Firma Adı</th>
                                <th class="px-6 py-4">VKN / TCKN</th>
                                <th class="px-6 py-4">Adres / Vergi Dairesi</th>
                                <th class="px-6 py-4">Tip</th>
                                <th class="px-6 py-4">E-Fatura Etiketi (Alias)</th>
                                <th class="px-6 py-4 text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($results as $res): ?>
                                <tr class="hover:bg-white/10 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-bold text-gray-100 group-hover:text-blue-400 transition">
                                                <?= htmlspecialchars($res['unvan']) ?>
                                            </div>
                                            <?php if (!empty($res['is_local'])): ?>
                                                <span
                                                    class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-500 font-bold border border-amber-500/20">REHBER</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($res['system_create_time'])): ?>
                                            <div class="text-[10px] text-gray-500 mt-1">Sistem Kayıt:
                                                <?= date('d.m.Y', strtotime($res['system_create_time'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-400"><?= $res['vkn'] ?></td>
                                    <td class="px-6 py-4">
                                        <?php if (!empty($res['adres'])): ?>
                                            <div class="text-[11px] text-gray-200 leading-tight mb-1">
                                                <i
                                                    class="fas fa-map-marker-alt text-blue-400 mr-1"></i><?= htmlspecialchars($res['adres']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($res['vergi_dairesi'])): ?>
                                            <div class="text-[10px] text-gray-500 italic">
                                                <i class="fas fa-landmark mr-1"></i><?= htmlspecialchars($res['vergi_dairesi']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (empty($res['adres']) && empty($res['vergi_dairesi'])): ?>
                                            <span class="text-[10px] text-gray-600 italic">Bilgi bulunamadı</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-bold uppercase tracking-tight">
                                            <?= htmlspecialchars($res['type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 max-w-[200px]">
                                            <span class="text-xs font-mono text-purple-400 truncate"
                                                title="<?= $res['alias'] ?>"><?= $res['alias'] ?></span>
                                            <button onclick="copyToClipboard('<?= $res['alias'] ?>')"
                                                class="p-1.5 hover:bg-white/10 rounded-lg text-gray-500 hover:text-white transition shadow-sm"
                                                title="Kopyala">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <?php
                                            $is_me = ($res['vkn'] === ($own_vkn ?? ''));
                                            $funny_invoice = "Kendine fatura mı keseceksin şakacı seni? 😂";
                                            $funny_add = "Kendini rehbere ekleyip ne yapacaksın, narsisizm merkezi mi burası? 😎";
                                            ?>

                                            <?php if ($is_me): ?>
                                                <div class="group/funny relative inline-block">
                                                    <button
                                                        class="px-3 py-1.5 bg-gray-500/10 text-gray-500 rounded-lg text-xs font-bold cursor-not-allowed flex items-center gap-1.5">
                                                        <i class="fas fa-ghost"></i> HEY SEN!
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-purple-600 text-white text-[10px] rounded-lg shadow-xl opacity-0 group-hover/funny:opacity-100 transition-opacity pointer-events-none z-50 text-center font-bold">
                                                        <?= $funny_invoice ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <a href="/fatura/yeni?vkn=<?= $res['vkn'] ?>&unvan=<?= urlencode($res['unvan']) ?>&alias=<?= urlencode($res['alias']) ?>&adres=<?= urlencode($res['adres'] ?? '') ?>&vd=<?= urlencode($res['vergi_dairesi'] ?? '') ?>"
                                                    class="px-3 py-1.5 bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                                    <i class="fas fa-file-invoice"></i> FATURA
                                                </a>
                                                <a href="/cari/yeni?vkn=<?= $res['vkn'] ?>&unvan=<?= urlencode($res['unvan']) ?>&alias=<?= urlencode($res['alias']) ?>&adres=<?= urlencode($res['adres'] ?? '') ?>&vd=<?= urlencode($res['vergi_dairesi'] ?? '') ?>"
                                                    class="px-3 py-1.5 bg-purple-500/10 hover:bg-purple-500 text-purple-400 hover:text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                                    <i class="fas fa-plus"></i> EKLE
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Instant Search Results (JS Rendered) -->
    <div id="resultsList" class="space-y-4 hidden">
        <h3 class="text-sm font-bold px-2 text-gray-400 flex items-center gap-2">
            <i class="fas fa-search text-blue-400"></i>
            Hızlı Sonuçlar (<span id="resultCount">0</span>)
        </h3>
        <div class="grid md:grid-cols-2 gap-3" id="resultsContainer"></div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const resultsList = document.getElementById('resultsList');
    const resultsContainer = document.getElementById('resultsContainer');
    const resultCount = document.getElementById('resultCount');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const detailCard = document.getElementById('detailCard');
    let searchTimeout = null;

    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();

        clearTimeout(searchTimeout);
        if (query.length < 3) {
            resultsList.classList.add('hidden'); // Hide instant results if query is too short
            return;
        }

        searchTimeout = setTimeout(() => {
            performInstantSearch(query);
        }, 500);
    });

    async function performInstantSearch(query) {
        // Show loading state in icon
        btnIcon.className = 'fas fa-spinner fa-spin';

        try {
            const formData = new FormData();
            formData.append('q', query);
            const response = await fetch('/api/mukellef/ara', {
                method: 'POST',
                body: formData
            });
            const json = await response.json();

            if (json.success && json.data.length > 0) {
                renderResults(json.data);
                if (document.getElementById('finalResults')) document.getElementById('finalResults').classList.add('hidden');
            } else {
                resultsList.classList.add('hidden'); // Hide results if no data
            }
        } catch (error) {
            console.error('Instant search error:', error);
            resultsList.classList.add('hidden'); // Hide results on error
        } finally {
            btnIcon.className = 'fas fa-arrow-right';
        }
    }

    function renderResults(data) {
        resultsList.classList.remove('hidden');
        resultCount.textContent = data.length;
        const ownVkn = '<?= $own_vkn ?? "" ?>';

        let html = '';
        data.forEach(res => {
            const isMe = res.vkn === ownVkn;
            const actionButtons = isMe
                ? `<div class="group/funny relative inline-block">
                    <button class="px-3 py-1.5 bg-gray-500/10 text-gray-500 rounded-lg text-xs font-bold cursor-not-allowed flex items-center gap-1.5">
                        <i class="fas fa-ghost"></i> HEY SEN!
                    </button>
                    <div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-purple-600 text-white text-[10px] rounded-lg shadow-xl opacity-0 group-hover/funny:opacity-100 transition-opacity pointer-events-none z-50 text-center font-bold">
                        Kendine fatura mı keseceksin şakacı seni? 😂
                    </div>
                </div>`
                : `<div class="flex items-center gap-2">
                    <form action="/mukellef/sorgula" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? "" ?>">
                        <input type="hidden" name="vkn" value="${res.vkn}">
                        <button type="submit" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-sm font-medium transition">
                            Detaylar
                        </button>
                    </form>
                </div>`;

            html += `
            <div class="glass rounded-xl p-4 flex items-center justify-between hover:bg-white/10 transition group border border-white/5 animate-fade-in">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-400">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-white group-hover:text-blue-400 transition flex items-center gap-2">
                            ${escapeHtml(res.unvan)}
                            ${res.is_local ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-500 font-bold border border-amber-500/20">REHBER</span>' : ''}
                        </div>
                        <div class="text-[10px] text-gray-500 font-mono">${res.vkn} | ${res.type}</div>
                        ${res.adres ? `<div class="text-[10px] text-gray-400 mt-1"><i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>${escapeHtml(res.adres)}</div>` : ''}
                        ${res.vergi_dairesi ? `<div class="text-[9px] text-gray-600 italic"><i class="fas fa-landmark mr-1"></i>${escapeHtml(res.vergi_dairesi)}</div>` : ''}
                    </div>
                </div>
                ${actionButtons}
            </div>`;
        });
        resultsContainer.innerHTML = html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('searchForm').addEventListener('submit', function () {
        const btn = document.getElementById('searchBtn');
        btn.disabled = true;
        btn.classList.add('opacity-70');
        btnText.textContent = 'Sorgulanıyor...';
        btnIcon.className = 'fas fa-spinner fa-spin';
    });
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Kopyalandı!',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }
</script>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }
</style>