<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header/Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="/cari" class="text-gray-400 hover:text-white flex items-center gap-2 transition group">
            <div
                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-white/10 transition">
                <i class="fas fa-arrow-left"></i>
            </div>
            <span>Cariler Listesine Dön</span>
        </a>

        <div class="flex gap-2">
            <a href="/fatura/yeni?vkn=<?= $cari['vkn'] ?? '' ?>&unvan=<?= urlencode($cari['unvan'] ?? '') ?>"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-file-invoice"></i>
                Fatura Kes
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <?php if ($cari): ?>
        <form action="/cari/<?= htmlspecialchars($cari['vkn'] ?? '') ?>" method="POST" id="cariForm">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

            <div class="glass rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
                <!-- Card Header (Visual) -->
                <div class="bg-gradient-to-r from-purple-600/20 to-pink-600/20 p-8 border-b border-white/10">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center text-3xl font-bold shadow-lg flex-shrink-0">
                            <?= strtoupper(substr($cari['unvan'] ?? 'C', 0, 2)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <!-- View Title -->
                            <h1 id="view-unvan" class="text-2xl lg:text-3xl font-bold text-white truncate">
                                <?= htmlspecialchars($cari['unvan'] ?? '-') ?></h1>
                            <!-- Edit Title -->
                            <input type="text" name="unvan" id="edit-unvan"
                                class="hidden w-full bg-white/10 border border-white/10 rounded-lg px-3 py-2 text-white text-xl lg:text-2xl font-bold focus:outline-none focus:border-purple-500"
                                value="<?= htmlspecialchars($cari['unvan'] ?? '') ?>">

                            <div class="flex items-center gap-3 mt-2">
                                <span
                                    class="text-purple-400 font-mono tracking-wider"><?= htmlspecialchars($cari['vkn'] ?? '-') ?></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>

                                <div id="view-grup">
                                    <span
                                        class="text-xs px-2 py-0.5 rounded bg-purple-500/20 text-purple-400 border border-purple-500/20"><?= htmlspecialchars($cari['grup'] ?? 'Genel') ?></span>
                                </div>
                                <div id="edit-grup" class="hidden">
                                    <select name="grup"
                                        class="bg-gray-800 border border-white/10 rounded px-2 py-0.5 text-xs text-white">
                                        <option value="Genel" <?= ($cari['grup'] ?? '') === 'Genel' ? 'selected' : '' ?>>Genel
                                        </option>
                                        <option value="Müşteri" <?= ($cari['grup'] ?? '') === 'Müşteri' ? 'selected' : '' ?>>
                                            Müşteri</option>
                                        <option value="Tedarikçi" <?= ($cari['grup'] ?? '') === 'Tedarikçi' ? 'selected' : '' ?>>Tedarikçi</option>
                                        <option value="Personel" <?= ($cari['grup'] ?? '') === 'Personel' ? 'selected' : '' ?>>
                                            Personel</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="toggleEdit"
                            class="p-3 bg-white/5 hover:bg-white/10 rounded-xl transition text-gray-400 hover:text-white"
                            title="Düzenle">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-8 grid md:grid-cols-2 gap-8">
                    <!-- Left Column: İletişim & Kurumsal -->
                    <div class="space-y-8">
                        <!-- İletişim -->
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold border-b border-white/5 pb-2">İletişim Bilgileri</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0"><i class="fas fa-phone"></i></div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 block">Telefon</label>
                                        <span id="view-telefon" class="text-gray-200"><?= htmlspecialchars($cari['telefon'] ?? 'Belirtilmedi') ?></span>
                                        <input type="text" name="telefon" id="edit-telefon" class="hidden w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1 text-white text-sm focus:outline-none focus:border-blue-500" value="<?= htmlspecialchars($cari['telefon'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400 shrink-0"><i class="fas fa-envelope"></i></div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 block">E-Posta</label>
                                        <span id="view-email" class="text-gray-200"><?= htmlspecialchars($cari['email'] ?? 'Belirtilmedi') ?></span>
                                        <input type="email" name="email" id="edit-email" class="hidden w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1 text-white text-sm focus:outline-none focus:border-red-500" value="<?= htmlspecialchars($cari['email'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kurumsal Bilgiler -->
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold border-b border-white/5 pb-2">🏢 Kurumsal Bilgiler</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] text-gray-500 block uppercase">Vergi Dairesi</label>
                                    <span class="text-sm text-gray-300"><?= htmlspecialchars($cari['vergi_dairesi'] ?? 'Sorgulanıyor...') ?></span>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block uppercase">Şehir</label>
                                    <span class="text-sm text-gray-300"><?= htmlspecialchars($cari['sehir'] ?? '-') ?></span>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block uppercase">Mükellef Tipi</label>
                                    <span class="text-sm text-gray-300"><?= htmlspecialchars($cari['tip'] ?? '-') ?></span>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block uppercase">Durum</label>
                                    <span class="text-xs px-2 py-0.5 rounded bg-green-500/20 text-green-400 inline-block mt-0.5">Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- Adres -->
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold border-b border-white/5 pb-2">Adres</h3>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400 shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="flex-1">
                                    <span id="view-adres" class="text-sm text-gray-200 leading-relaxed"><?= nl2br(htmlspecialchars($cari['adres'] ?? 'Belirtilmedi')) ?></span>
                                    <textarea name="adres" id="edit-adres" rows="3" class="hidden w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1 text-white text-sm focus:outline-none focus:border-green-500"><?= htmlspecialchars($cari['adres'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Etiketler & Sistem -->
                    <div class="space-y-8">
                        <!-- Etiket Section -->
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold border-b border-white/5 pb-2">🏷️ Etiketler (Alias)</h3>
                            <?php if (!empty($cari['alias'])): ?>
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-4">
                                    <div class="flex justify-between items-center group">
                                        <div class="flex-1 overflow-hidden">
                                            <span class="text-[10px] text-gray-500 block uppercase mb-1">E-Fatura Etiketi (PK)</span>
                                            <span class="text-xs font-mono text-purple-400 truncate block"><?= $cari['alias'] ?></span>
                                        </div>
                                        <button type="button" onclick="copyToClipboard('<?= $cari['alias'] ?>')" class="p-2 hover:bg-white/10 rounded-lg transition text-gray-400 hover:text-white" title="Kopyala">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="text-gray-500">Kayıt Tarihi:</span>
                                        <span class="text-gray-400"><?= !empty($cari['kayit_tarihi']) ? date('d.m.Y', strtotime($cari['kayit_tarihi'])) : '-' ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500 py-4 italic text-center">Mükellef etiket bilgisi bulunamadı.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Sistem Bilgileri -->
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold border-b border-white/5 pb-2">Sistem Bilgileri</h3>
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Eklenme Tarihi</span>
                                    <span class="text-gray-300"><?= !empty($cari['created_at']) ? date('d.m.Y H:i', strtotime($cari['created_at'])) : '-' ?></span>
                                </div>
                                <?php if (!empty($cari['updated_at'])): ?>
                                <div class="flex justify-between items-center text-sm border-t border-white/5 pt-4">
                                    <span class="text-gray-500">Son Güncelleme</span>
                                    <span class="text-gray-300"><?= date('d.m.Y H:i', strtotime($cari['updated_at'])) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Edit Buttons -->
                        <div id="edit-actions" class="hidden space-y-3 pt-4">
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-600 to-green-700 hover:shadow-lg hover:shadow-green-500/30 rounded-xl font-bold text-white transition flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <button type="button" id="cancelEdit" class="w-full py-3 bg-white/5 hover:bg-white/10 rounded-xl font-semibold text-gray-400 transition">İptal Et</button>
                        </div>

                        <div id="view-actions" class="p-6 rounded-2xl bg-gradient-to-br from-blue-600/10 to-purple-600/10 border border-blue-500/10 text-center">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400 mb-4 mx-auto"><i class="fas fa-info-circle"></i></div>
                            <p class="text-sm text-gray-400 leading-relaxed">Bilgileri kontrol edebilir veya hızlıca fatura oluşturabilirsiniz.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <!-- Not Found State -->
        <div class="glass rounded-2xl p-12 text-center animate-fade-in">
            <div
                class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center text-3xl text-red-400 mx-auto mb-6">
                <i class="fas fa-user-slash"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Cari Bulunamadı</h2>
            <p class="text-gray-400 max-w-sm mx-auto">
                Aradığınız cari kaydı veritabanında bulunamadı veya silinmiş olabilir.
            </p>
            <a href="/cari"
                class="inline-block mt-8 px-8 py-3 bg-white/5 hover:bg-white/10 rounded-xl transition font-semibold">
                Listeye Geri Dön
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    const toggleBtn = document.getElementById('toggleEdit');
    const cancelBtn = document.getElementById('cancelEdit');
    const editElements = ['edit-unvan', 'edit-grup', 'edit-telefon', 'edit-email', 'edit-adres', 'edit-actions'];
    const viewElements = ['view-unvan', 'view-grup', 'view-telefon', 'view-email', 'view-adres', 'view-actions'];

    toggleBtn?.addEventListener('click', () => {
        const isEditing = !document.getElementById('edit-unvan').classList.contains('hidden');
        toggleMode(!isEditing);
    });

    cancelBtn?.addEventListener('click', () => toggleMode(false));

    function toggleMode(edit) {
        editElements.forEach(id => {
            document.getElementById(id)?.classList.toggle('hidden', !edit);
        });
        viewElements.forEach(id => {
            document.getElementById(id)?.classList.toggle('hidden', edit);
        });
        toggleBtn.innerHTML = edit ? '<i class="fas fa-times"></i>' : '<i class="fas fa-edit"></i>';
        toggleBtn.classList.toggle('bg-red-500/20', edit);
        toggleBtn.classList.toggle('text-red-400', edit);
    }
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
</div>

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
        animation: fade-in 0.4s ease-out forwards;
    }
</style>