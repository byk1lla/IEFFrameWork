<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Yeni Cari Ekle</h1>
            <p class="text-gray-400">Rehbere yeni bir müşteri veya tedarikçi kaydedin.</p>
        </div>
        <a href="/cari" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Vazgeç
        </a>
    </div>

    <div class="glass rounded-2xl p-8 border border-white/5 shadow-2xl">
        <form action="/cari/kaydet" method="POST" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

            <div class="grid md:grid-cols-2 gap-6">
                <!-- VKN / TCKN -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">VKN / TCKN</label>
                    <input type="text" name="vkn" value="<?= htmlspecialchars($_GET['vkn'] ?? '') ?>" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Ünvan -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ünvan / Firma Adı</label>
                    <input type="text" name="unvan" value="<?= htmlspecialchars($_GET['unvan'] ?? '') ?>" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Grup -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Grup</label>
                    <select name="grup"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="Genel" class="bg-gray-900 text-white">Genel</option>
                        <option value="Müşteri" class="bg-gray-900 text-white">Müşteri</option>
                        <option value="Tedarikçi" class="bg-gray-900 text-white">Tedarikçi</option>
                        <option value="Önemli" class="bg-gray-900 text-white">Önemli</option>
                    </select>
                </div>

                <!-- E-Posta -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">E-Posta</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Telefon -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Telefon</label>
                    <input type="text" name="telefon" value="<?= htmlspecialchars($_GET['telefon'] ?? '') ?>"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition"
                        placeholder="05XX-XXX-XXXX">
                </div>

                <!-- Adres -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Adres</label>
                    <textarea name="adres" rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 transition"><?= htmlspecialchars($_GET['adres'] ?? '') ?></textarea>
                </div>

                <!-- Alias (Hidden but carried) -->
                <input type="hidden" name="alias" value="<?= htmlspecialchars($_GET['alias'] ?? '') ?>">
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-save"></i>
                    Kaydet ve Rehbere Ekle
                </button>
            </div>
        </form>
    </div>
</div>