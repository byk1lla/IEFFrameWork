<!-- Cari Listesi -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold">👥 Cariler</h1>
            <p class="text-gray-400 mt-1">Müşteri ve tedarikçi kayıtları</p>
        </div>
        <a href="/cari/yeni"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/30 transition">
            <i class="fas fa-plus"></i>
            <span>Yeni Cari</span>
        </a>
    </div>

    <!-- Search -->
    <div class="glass rounded-xl p-4">
        <input type="text" id="cariSearch" placeholder="VKN, ünvan veya telefon ile ara..."
            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-purple-500">
    </div>

    <!-- Cari Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4" id="cariGrid">
        <?php if (empty($cariler)): ?>
            <div class="col-span-full glass rounded-2xl p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400">Henüz cari kaydı yok</p>
                <a href="/cari/yeni" class="inline-block mt-4 text-purple-400 hover:text-purple-300">
                    <i class="fas fa-plus mr-1"></i> İlk carinizi ekleyin
                </a>
            </div>
        <?php else: ?>

            <?php foreach ($cariler as $vkn => $cari): ?>
                <div class="glass rounded-2xl p-5 card-hover cursor-pointer cari-card"
                    data-search="<?= htmlspecialchars(strtolower(($cari['vkn'] ?? '') . ' ' . ($cari['unvan'] ?? '') . ' ' . ($cari['telefon'] ?? ''))) ?>"
                    onclick="window.location='/cari/<?= urlencode($vkn) ?>'">

                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center font-bold text-lg flex-shrink-0">
                            <?= strtoupper(substr($cari['unvan'] ?? 'C', 0, 2)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold truncate">
                                <?= htmlspecialchars($cari['unvan'] ?? '-') ?>
                            </h3>
                            <p class="text-sm text-gray-400 mt-1">
                                <i class="fas fa-id-card mr-1"></i>
                                <?= htmlspecialchars($vkn) ?>
                            </p>
                            <?php if (!empty($cari['telefon'])): ?>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-phone mr-1"></i>
                                    <?= htmlspecialchars($cari['telefon']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($cari['grup'])): ?>
                                <span class="inline-block mt-2 text-xs bg-purple-500/20 text-purple-400 px-2 py-1 rounded">
                                    <?= htmlspecialchars($cari['grup']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-gray-500 hover:text-white transition">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<script>
    // Cari arama
    document.getElementById('cariSearch')?.addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.cari-card').forEach(card => {
            const searchText = card.dataset.search || '';
            card.style.display = searchText.includes(query) ? '' : 'none';
        });
    });
</script>