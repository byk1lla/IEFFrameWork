<!-- Yeni Hizmet Ekle -->
<div class="max-w-2xl mx-auto space-y-6 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="/hizmetler" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold">Yeni Hizmet Ekle</h1>
            <p class="text-gray-400 mt-1">Fatura kalemleri için şablon oluştur</p>
        </div>
    </div>

    <form action="/hizmet/kaydet" method="POST" class="space-y-6">
        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

        <div class="glass rounded-2xl p-6 space-y-6">

            <!-- Hizmet Adı -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Hizmet / Ürün Adı</label>
                <input type="text" name="name" required
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-purple-500 placeholder-gray-500"
                    placeholder="Örn: Yazılım Geliştirme Hizmeti">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Birim Fiyat -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Birim Fiyat (KDV Hariç)</label>
                    <div class="relative">
                        <input type="number" name="price" step="0.01" min="0" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-purple-500"
                            placeholder="0.00">
                        <span class="absolute right-4 top-3 text-gray-400">₺</span>
                    </div>
                </div>

                <!-- KDV Oranı -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">KDV Oranı</label>
                    <select name="tax"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-purple-500">
                        <option value="20">%20</option>
                        <option value="10">%10</option>
                        <option value="1">%1</option>
                        <option value="0">%0</option>
                    </select>
                </div>
            </div>

            <!-- Birim -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Birim</label>
                <select name="unit"
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-purple-500">
                    <option value="ADET">ADET</option>
                    <option value="SAAT">SAAT</option>
                    <option value="GUN">GÜN</option>
                    <option value="AY">AY</option>
                    <option value="YIL">YIL</option>
                    <option value="KG">KG</option>
                    <option value="LT">LT</option>
                    <option value="MT">MT</option>
                </select>
            </div>

            <!-- Varsayılan Checkbox -->
            <div class="pt-4 border-t border-white/10">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="is_default" class="sr-only peer">
                        <div class="w-10 h-6 bg-white/10 rounded-full peer-checked:bg-green-500 transition"></div>
                        <div
                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4">
                        </div>
                    </div>
                    <div>
                        <span class="font-medium text-white group-hover:text-green-400 transition">Varsayılan Hizmet
                            Yap</span>
                        <p class="text-xs text-gray-400">Yeni fatura oluşturulurken bu hizmet otomatik seçili gelir.</p>
                    </div>
                </label>
            </div>

        </div>

        <button type="submit"
            class="w-full py-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/30 transition">
            Hizmeti Kaydet
        </button>
    </form>
</div>