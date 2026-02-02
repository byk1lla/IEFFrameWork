<!-- Yeni Fatura Oluştur -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="/fatura" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold">📝 Yeni Fatura</h1>
            <p class="text-gray-400 mt-1">E-Fatura oluştur ve gönder</p>
        </div>
    </div>

    <form action="/fatura/kaydet" method="POST" class="space-y-6">
        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

        <!-- Alıcı Bilgileri -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-400"></i>
                Alıcı Bilgileri
            </h2>

            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">VKN / TCKN</label>
                    <div class="flex gap-2">
                        <input type="text" name="vkn" id="vkn" required maxlength="11"
                            class="flex-1 bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                            placeholder="VKN veya TCKN girin" hx-post="/api/vkn/sorgula" hx-trigger="blur"
                            hx-target="#alici_bilgi">
                        <button type="button" onclick="sorgulaVKN()"
                            class="px-4 bg-blue-500 rounded-xl hover:bg-blue-600 transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Ünvan / Ad Soyad</label>
                    <input type="text" name="unvan" id="unvan" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                        placeholder="Firma ünvanı veya ad soyad">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-gray-400 text-sm mb-2">Adres</label>
                    <textarea name="adres" rows="2"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                        placeholder="Firma adresi"></textarea>
                </div>

                <div id="alici_bilgi" class="lg:col-span-2"></div>
            </div>
        </div>

        <!-- Fatura Bilgileri -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-file-invoice text-green-400"></i>
                Fatura Bilgileri
            </h2>

            <div class="grid lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Fatura Tipi</label>
                    <select name="fatura_tipi" id="fatura_tipi"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="SATIS">SATIŞ</option>
                        <option value="IADE">İADE</option>
                        <option value="TEVKIFAT">TEVKİFAT</option>
                        <option value="ISTISNA">İSTİSNA</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Para Birimi</label>
                    <select name="para_birimi"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="TRY">TRY - Türk Lirası</option>
                        <option value="USD">USD - Amerikan Doları</option>
                        <option value="EUR">EUR - Euro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Fatura Tarihi</label>
                    <input type="date" name="fatura_tarihi" value="<?= date('Y-m-d') ?>"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Hizmet/Ürün Detayı -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-box text-purple-400"></i>
                Hizmet / Ürün Detayı
            </h2>

            <div class="grid lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-gray-400 text-sm mb-2">Hizmet Adı</label>
                    <input type="text" name="hizmet_adi" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                        placeholder="Örn: Yazılım Geliştirme Hizmeti">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Miktar</label>
                    <input type="number" name="miktar" value="1" min="1" step="0.01"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Birim Fiyat (₺)</label>
                    <input type="number" name="birim_fiyat" required step="0.01" min="0"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">KDV Oranı</label>
                    <select name="kdv_orani"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="20">%20</option>
                        <option value="10">%10</option>
                        <option value="1">%1</option>
                        <option value="0">%0 (İstisna)</option>
                    </select>
                </div>

                <div id="tevkifat_alan" class="hidden">
                    <label class="block text-gray-400 text-sm mb-2">Tevkifat Kodu</label>
                    <select name="tevkifat_kodu"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="624">624 - Yük Taşımacılığı</option>
                        <option value="613">613 - Yapım İşleri</option>
                        <option value="616">616 - Temizlik Hizmeti</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Notlar -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-sticky-note text-yellow-400"></i>
                Notlar
            </h2>
            <textarea name="notlar" rows="3"
                class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                placeholder="Fatura üzerinde görünecek ekstra notlar..."></textarea>
        </div>

        <!-- Actions -->
        <div class="flex flex-col lg:flex-row gap-4">
            <button type="submit" name="action" value="taslak"
                class="flex-1 py-4 bg-white/10 rounded-xl font-semibold hover:bg-white/20 transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i>
                Taslak Olarak Kaydet
            </button>
            <button type="submit" name="action" value="gonder"
                class="flex-1 py-4 bg-gradient-to-r from-green-500 to-green-600 rounded-xl font-semibold hover:shadow-lg hover:shadow-green-500/30 transition flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i>
                Faturayı Gönder
            </button>
        </div>
    </form>
</div>

<script>
    // Tevkifat alanını göster/gizle
    document.getElementById('fatura_tipi').addEventListener('change', function () {
        const tevkifatAlan = document.getElementById('tevkifat_alan');
        tevkifatAlan.classList.toggle('hidden', this.value !== 'TEVKIFAT');
    });

    // VKN Sorgula
    function sorgulaVKN() {
        const vkn = document.getElementById('vkn').value;
        if (vkn.length < 10) {
            Swal.fire('Uyarı', 'VKN en az 10 karakter olmalıdır', 'warning');
            return;
        }

        fetch('/api/vkn/sorgula', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ vkn: vkn })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.mukellef) {
                    document.getElementById('unvan').value = data.mukellef.Unvan || '';
                    Swal.fire('Başarılı', 'Mükellef bilgileri getirildi', 'success');
                } else {
                    Swal.fire('Hata', data.message || 'Mükellef bulunamadı', 'error');
                }
            })
            .catch(err => Swal.fire('Hata', 'Bağlantı hatası', 'error'));
    }
</script>