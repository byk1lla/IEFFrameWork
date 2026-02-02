<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold">📦 Hizmet ve Ürünler</h1>
            <p class="text-gray-400 mt-1">Sık kullanılan hizmet ve ürünlerinizi yönetin</p>
        </div>
        <a href="/hizmet/yeni"
            class="px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/30 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Yeni Ekle</span>
        </a>
    </div>

    <!-- Hizmet Listesi -->
    <div class="glass rounded-2xl p-6">
        <?php if (empty($services)): ?>
            <div class="text-center py-12 text-gray-400">
                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-3xl"></i>
                </div>
                <p class="text-lg">Henüz kayıtlı hizmet veya ürün yok.</p>
                <p class="text-sm mt-2">Sık kullandığınız kalemleri ekleyerek fatura oluştururken zaman kazanın.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-white/10">
                            <th class="pb-4 pl-4">Hizmet Adı</th>
                            <th class="pb-4">Birim Fiyat</th>
                            <th class="pb-4">KDV</th>
                            <th class="pb-4">Birim</th>
                            <th class="pb-4 text-center">Varsayılan</th>
                            <th class="pb-4 pr-4 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($services as $service): ?>
                            <tr class="group hover:bg-white/5 transition">
                                <td class="py-4 pl-4 font-medium">
                                    <?= htmlspecialchars($service['name']) ?>
                                </td>
                                <td class="py-4">
                                    <?= number_format($service['price'], 2, ',', '.') ?> ₺
                                </td>
                                <td class="py-4">%
                                    <?= $service['tax'] ?>
                                </td>
                                <td class="py-4">
                                    <?= $service['unit'] ?>
                                </td>
                                <td class="py-4 text-center">
                                    <?php if (!empty($service['is_default'])): ?>
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-green-500/20 text-green-400 text-xs font-semibold">
                                            <i class="fas fa-check-circle"></i> Varsayılan
                                        </span>
                                    <?php else: ?>
                                        <a href="/hizmet/varsayilan/<?= $service['id'] ?>"
                                            class="text-gray-500 hover:text-white transition text-xs opacity-0 group-hover:opacity-100"
                                            title="Varsayılan Yap">
                                            <i class="far fa-star"></i> Yap
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 pr-4 text-right">
                                    <a href="/hizmet/sil/<?= $service['id'] ?>"
                                        onclick="return confirm('Bu hizmeti silmek istediğinize emin misiniz?')"
                                        class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition inline-flex">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>