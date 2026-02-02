<!-- Fatura Listesi -->
<div class="space-y-6">
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

    <!-- Error Message -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="glass rounded-xl p-4 flex flex-wrap gap-4">
        <select class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm">
            <option>Tüm Faturalar</option>
            <option>Giden (Satış)</option>
            <option>Gelen (Alış)</option>
        </select>
        <select class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm">
            <option>Son 30 Gün</option>
            <option>Son 90 Gün</option>
            <option>Bu Ay</option>
            <option>Geçen Ay</option>
        </select>
        <input type="text" placeholder="Fatura No veya Firma Ara..."
            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-sm min-w-[200px]">
    </div>

    <!-- Invoice List -->
    <div class="glass rounded-2xl overflow-hidden">
        <?php if (empty($faturalar)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-file-invoice text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400">Henüz fatura bulunamadı</p>
                <a href="/fatura/yeni" class="inline-block mt-4 text-blue-400 hover:text-blue-300">
                    <i class="fas fa-plus mr-1"></i> İlk faturanızı oluşturun
                </a>
            </div>
        <?php else: ?>

            <!-- Mobile View -->
            <div class="lg:hidden divide-y divide-white/10">
                <?php foreach ($faturalar as $fatura): ?>
                    <div class="p-4 hover:bg-white/5 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium">
                                    <?= htmlspecialchars($fatura->ID ?? '-') ?>
                                </p>
                                <p class="text-sm text-gray-400 mt-1">
                                    <?= htmlspecialchars($fatura->ReceiverName ?? '-') ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= isset($fatura->CreateDate) ? date('d.m.Y', strtotime($fatura->CreateDate)) : '-' ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-400">
                                    ₺
                                    <?= number_format(floatval($fatura->PayableAmount ?? 0), 2, ',', '.') ?>
                                </p>
                                <?php
                                $status = $fatura->Status ?? '';
                                $statusClass = 'bg-yellow-500/20 text-yellow-400';
                                $statusText = 'Bekliyor';
                                if (stripos($status, 'SUCCEED') !== false || stripos($status, 'APPROVED') !== false) {
                                    $statusClass = 'bg-green-500/20 text-green-400';
                                    $statusText = 'Onaylı';
                                } elseif (stripos($status, 'REJECT') !== false || stripos($status, 'FAILED') !== false) {
                                    $statusClass = 'bg-red-500/20 text-red-400';
                                    $statusText = 'Reddedildi';
                                }
                                ?>
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm" id="faturaTable">
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
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($faturalar as $fatura): ?>
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 px-6 font-medium">
                                    <?= htmlspecialchars($fatura->ID ?? '-') ?>
                                </td>
                                <td class="py-4 px-6">
                                    <?= htmlspecialchars($fatura->ReceiverName ?? '-') ?>
                                </td>
                                <td class="py-4 px-6 text-gray-400">
                                    <?= isset($fatura->CreateDate) ? date('d.m.Y', strtotime($fatura->CreateDate)) : '-' ?>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded">
                                        <?= htmlspecialchars($fatura->InvoiceType ?? 'SATIS') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-semibold">
                                    ₺
                                    <?= number_format(floatval($fatura->PayableAmount ?? 0), 2, ',', '.') ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php
                                    $status = $fatura->Status ?? '';
                                    $statusClass = 'bg-yellow-500/20 text-yellow-400';
                                    $statusText = 'Bekliyor';
                                    if (stripos($status, 'SUCCEED') !== false || stripos($status, 'APPROVED') !== false) {
                                        $statusClass = 'bg-green-500/20 text-green-400';
                                        $statusText = 'Onaylı';
                                    } elseif (stripos($status, 'REJECT') !== false || stripos($status, 'FAILED') !== false) {
                                        $statusClass = 'bg-red-500/20 text-red-400';
                                        $statusText = 'Reddedildi';
                                    }
                                    ?>
                                    <span class="text-xs px-2 py-1 rounded <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="/fatura/<?= urlencode($fatura->UUID ?? '') ?>"
                                        class="text-blue-400 hover:text-blue-300 mx-1" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="text-green-400 hover:text-green-300 mx-1" title="PDF İndir">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable && $('#faturaTable').length) {
            $('#faturaTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
                },
                pageLength: 25,
                order: [[2, 'desc']]
            });
        }
    });
</script>