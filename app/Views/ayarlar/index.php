<!-- Ayarlar -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold">⚙️ Ayarlar</h1>
        <p class="text-gray-400 mt-1">Sistem ve EDM bağlantı ayarları</p>
    </div>

    <!-- Flash Messages -->
    <?php if ($success = \App\Core\Session::getFlash('success')): ?>
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>
                <?= htmlspecialchars($success) ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- EDM Bağlantı Ayarları -->
        <form action="/ayarlar/kaydet" method="POST" class="glass rounded-2xl p-6">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-plug text-green-400"></i>
                EDM Bilişim Bağlantısı
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">EDM Kullanıcı Adı</label>
                    <input type="text" name="edm_username" value="<?= htmlspecialchars($edm_username ?? '') ?>"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-green-500">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">EDM Şifre</label>
                    <input type="password" name="edm_password" placeholder="••••••••"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-green-500">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-green-500/30 transition">
                    <i class="fas fa-save mr-2"></i>
                    Kaydet
                </button>
            </div>
        </form>

        <!-- Firma Bilgileri -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-building text-blue-400"></i>
                Firma Bilgileri
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">Kullanıcı</span>
                    <span class="font-medium">
                        <?= htmlspecialchars($authUser['username'] ?? '-') ?>
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">Rol</span>
                    <span class="font-medium">
                        <?= $authUser['is_admin'] ?? false ? 'Admin' : 'Kullanıcı' ?>
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">EDM Bağlantı</span>
                    <span class="<?= !empty($edm_username) ? 'text-green-400' : 'text-yellow-400' ?>">
                        <?= !empty($edm_username) ? '✓ Aktif' : '○ Yapılandırılmadı' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Uygulama Bilgileri -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-purple-400"></i>
                Uygulama Bilgileri
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">Versiyon</span>
                    <span class="font-medium">v2.0.0</span>
                </div>
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">Framework</span>
                    <span class="font-medium">IEF Framework</span>
                </div>
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-gray-400">PHP Versiyon</span>
                    <span class="font-medium">
                        <?= PHP_VERSION ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="glass rounded-2xl p-6 border border-red-500/30">
            <h3 class="font-semibold mb-4 flex items-center gap-2 text-red-400">
                <i class="fas fa-exclamation-triangle"></i>
                Tehlikeli Bölge
            </h3>

            <div class="space-y-4">
                <button onclick="confirm('Cache temizlenecek. Emin misiniz?') && fetch('/api/cache/clear')"
                    class="w-full py-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition text-left px-4">
                    <i class="fas fa-broom text-yellow-400 mr-2"></i>
                    Cache Temizle
                </button>
                <a href="/logout"
                    class="block w-full py-3 bg-red-500/20 border border-red-500/30 rounded-xl hover:bg-red-500/30 transition text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Çıkış Yap
                </a>
            </div>
        </div>
    </div>
</div>