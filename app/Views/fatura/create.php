<!-- Fatura Oluştur -->
<div class="space-y-6 pb-20">
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

    <form id="invoiceForm" action="/fatura/kaydet" method="POST" class="space-y-6" onsubmit="return validateForm()">
        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

        <!-- Alıcı Bilgileri -->
        <div class="glass rounded-2xl p-6 relative z-10">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-400"></i>
                Alıcı Bilgileri
            </h2>

            <div class="grid lg:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">VKN / TCKN</label>
                    <div class="flex gap-2">
                        <input type="text" name="vkn" id="vkn" required maxlength="11"
                            class="flex-1 bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                            placeholder="VKN giriniz" onblur="if(this.value.length >= 10) sorgulaVKN()">
                        <button type="button" onclick="sorgulaVKN()"
                            class="px-4 bg-blue-500 rounded-xl hover:bg-blue-600 transition" title="VKN ile Sorgula">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-gray-400 text-sm mb-2">Ünvan / Ad Soyad</label>
                    <div class="relative">
                        <input type="text" name="unvan" id="unvan" required autocomplete="off"
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 focus:bg-gray-800"
                            placeholder="Firma ünvanı yazarak arayın..." oninput="handleTitleInput(this.value)">
                        
                        <!-- Small Ring Spinner CSS for this page -->
                        <style>
                            .small-ring-spinner {
                                width: 20px;
                                height: 20px;
                                border: 2px solid rgba(255, 255, 255, 0.1);
                                border-top-color: #3b82f6; 
                                border-radius: 50%;
                                animation: spin 0.8s linear infinite;
                            }
                            @keyframes spin { to { transform: rotate(360deg); } }
                        </style>

                        <div id="title_suggestions"
                            class="hidden absolute top-full left-0 right-0 mt-1 bg-gray-800 border border-white/10 rounded-xl shadow-2xl max-h-60 overflow-y-auto z-50">
                        </div>
                    </div>
                    <div id="title_loading" class="hidden absolute right-4 top-[38px]">
                         <div class="small-ring-spinner"></div>
                    </div>
                </div>
            </div>

            <!-- Adres Alanı - Fixed Input issues -->
            <div class="lg:col-span-2 relative z-0">
                <label class="block text-gray-400 text-sm mb-2">Adres</label>
                <textarea name="adres" id="adres" rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 placeholder-gray-500"
                    placeholder="Tam adres giriniz"></textarea>
            </div>

            <div id="alici_bilgi" class="mt-4 empty:hidden"></div>
        </div>

        <!-- Fatura Ayarları -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-cog text-gray-400"></i>
                Fatura Ayarları
            </h2>

            <div class="grid lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Fatura Tipi</label>
                    <select name="fatura_tipi" id="fatura_tipi" onchange="toggleTevkifat()"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="SATIS">SATIŞ</option>
                        <option value="TEVKIFAT">TEVKİFAT</option>
                        <!-- Other types removed as per request -->
                    </select>
                </div>

                <div id="tevkifat_container" class="hidden lg:col-span-2">
                    <label class="block text-gray-400 text-sm mb-2">Tevkifat Kodu</label>
                    <select name="tevkifat_kodu" id="tevkifat_kodu"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500">
                        <option value="601">601 - YAPIM İŞLERİ İLE BU İŞLERLE BİRLİKTE İFA EDİLEN MÜHENDİSLİK-MİMARLIK
                            VE ETÜT-PROJE HİZMETLERİ</option>
                        <option value="602">602 - ETÜT, PLAN-PROJE, DANIŞMANLIK, DENETİM VE BENZERİ HİZMETLER</option>
                        <option value="603">603 - MAKİNE, TEÇHİZAT, DEMİRBAŞ VE TAŞITLARA AİT TADİL, BAKIM VE ONARIM
                            HİZMETLERİ</option>
                        <option value="604">604 - YEMEK SERVİS HİZMETİ</option>
                        <option value="605">605 - ORGANİZASYON HİZMETİ</option>
                        <option value="606">606 - İŞGÜCÜ TEMİN HİZMETLERİ</option>
                        <option value="607">607 - ÖZEL GÜVENLİK HİZMETİ</option>
                        <option value="608">608 - YAPI DENETİM HİZMETLERİ</option>
                        <option value="609">609 - TURİSTİK MAĞAZALARA VERİLEN MÜŞTERİ BULMA / GÖTÜRME HİZMETLERİ
                        </option>
                        <option value="610">610 - SPOR KULÜPLERİNİN YAYIN, REKLAM VE İSİM HAKKI GELİRLERİNE KONU
                            İŞLEMLERİ</option>
                        <option value="611" >611 - TAŞIMACILIK HİZMETLERİ</option> <option value="612">612 - SERVİS
                            TAŞIMACILIĞI HİZMETİ</option>
                        <option value="613">613 - BASILI KİTAP VE SÜRELİ YAYINLARIN TESLİMİ</option>
                        <option value="614">614 - HER TÜRLÜ BASKI VE BASIM HİZMETLERİ</option>
                        <option value="615">615 - HURDA VE ATIK TESLİMİ</option>
                        <option value="616">616 - METAL, PLASTİK, LASTİK, KAUÇUK, KAĞIT, CAM HURDA VE ATIKLARDAN ELDE
                            EDİLEN HAMMADDE TESLİMİ</option>
                        <option value="617">617 - PAMUK, TİFTİK, YÜN VE YAPAĞI İLE HAM POST VE DERİ TESLİMLERİ</option>
                        <option value="618">618 - AĞAÇ VE ORMAN ÜRÜNLERİ TESLİMİ</option>
                        <option value="619">619 - ÇİNKO, ALÜMİNYUM VE KURŞUN KÜLÇE TESLİMİ</option>
                        <option value="620">620 - BAKIR, ÇİNKO, ALÜMİNYUM VE KURŞUN ÜRÜNLERİNİN TESLİMİ</option>
                        <option value="621">621 - İKİNCİ EL MOTORLU KARA TAŞITI TİCARETİYLE İŞTİGAL EDEN MÜKELLEFLERCE
                            %18 KDV ORANI UYGULANARAK YAPILAN ARAÇ TESLİMLERİ</option>
                        <option value="622">622 - DİĞER HİZMETLER</option>
                        <option value="623">623 - DİĞER TESLİMLER</option>
                        <option value="624" selected>624 - YÜK TAŞIMACILIĞI HİZMETİ</option>
                        <option value="625">625 - TİCARİ REKLAM HİZMETLERİ</option>
                        <option value="626">626 - KÜLÇE METAL TESLİMLERİ</option>
                        <option value="627">627 - DEMİR-ÇELİK ÜRÜNLERİNİN TESLİMİ</option>
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

        <!-- Hizmet/Ürün Detayı (Multi-Row) -->
        <div class="glass rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-box text-purple-400"></i>
                    Hizmet / Ürün Detayı
                </h2>
                <button type="button" onclick="addRow()"
                    class="text-sm bg-purple-500/20 text-purple-400 px-3 py-1 rounded-lg hover:bg-purple-500/30 transition">
                    <i class="fas fa-plus mr-1"></i> Satır Ekle
                </button>
            </div>

            <div class="space-y-4" id="items-container">
                <!-- Rows will be added here via JS -->
            </div>

            <!-- Summary -->
            <div class="flex justify-end mt-6 pt-4 border-t border-white/10">
                <div class="w-full lg:w-1/3 space-y-2">
                    <div class="flex justify-between text-gray-400">
                        <span>Ara Toplam</span>
                        <span id="subtotal">0.00 ₺</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>KDV Toplam</span>
                        <span id="tax-total">0.00 ₺</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-white pt-2 border-t border-white/10">
                        <span>Genel Toplam</span>
                        <span id="grand-total">0.00 ₺</span>
                    </div>
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

<!-- Template for new row -->
<template id="item-row-template">
    <div class="grid lg:grid-cols-12 gap-3 item-row group relative bg-white/5 p-4 rounded-xl border border-white/5">
        <div class="lg:col-span-5">
            <label class="block text-gray-400 text-xs mb-1 lg:hidden">Hizmet Adı</label>
            <input type="text" name="items[{index}][name]" required
                class="w-full bg-transparent border-none text-white focus:ring-0 p-0 placeholder-gray-500 font-medium"
                placeholder="Hizmet veya ürün adı">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-gray-400 text-xs mb-1 lg:hidden">Miktar</label>
            <input type="number" name="items[{index}][qty]" value="1" min="1" step="0.01" oninput="calculateTotals()"
                class="w-full bg-transparent border-b border-white/10 text-white focus:border-blue-500 px-2 py-1 text-center">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-gray-400 text-xs mb-1 lg:hidden">Birim Fiyat</label>
            <input type="number" name="items[{index}][price]" step="0.01" min="0" oninput="calculateTotals()"
                class="w-full bg-transparent border-b border-white/10 text-white focus:border-blue-500 px-2 py-1 text-right"
                placeholder="0.00">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-gray-400 text-xs mb-1 lg:hidden">KDV</label>
            <select name="items[{index}][tax]" onchange="calculateTotals()"
                class="w-full bg-transparent border-b border-white/10 text-white focus:border-blue-500 px-2 py-1">
                <option value="20">%20</option>
                <option value="10">%10</option>
                <option value="1">%1</option>
                <option value="0">%0</option>
            </select>
        </div>
        <div class="lg:col-span-1 flex items-center justify-end">
            <button type="button" onclick="removeRow(this)"
                class="text-red-400 hover:text-red-300 transition opacity-0 group-hover:opacity-100">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    let rowCount = 0;

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        addRow(); // Add first row
        calculateTotals(); // Calculate initial totals
        toggleTevkifat(); // Set initial tevkifat visibility
    });

    function addRow() {
        const template = document.getElementById('item-row-template');
        const container = document.getElementById('items-container');
        const clone = template.content.cloneNode(true);

        // Rplace placeholders
        const html = clone.querySelector('div').outerHTML.replace(/{index}/g, rowCount++);

        // Append
        container.insertAdjacentHTML('beforeend', html);

        // Add listeners to new inputs if needed
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('.item-row').remove();
            calculateTotals();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'En az bir satır olmalıdır.',
                toast: true, position: 'top-end', timer: 2000, showConfirmButton: false
            });
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        let taxTotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('[name*="[qty]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name*="[price]"]').value) || 0;
            const taxRate = parseFloat(row.querySelector('[name*="[tax]"]').value) || 0;

            const lineTotal = qty * price;
            const lineTax = lineTotal * (taxRate / 100);

            subtotal += lineTotal;
            taxTotal += lineTax;
        });

        const grandTotal = subtotal + taxTotal;

        document.getElementById('subtotal').textContent = formatMoney(subtotal);
        document.getElementById('tax-total').textContent = formatMoney(taxTotal);
        document.getElementById('grand-total').textContent = formatMoney(grandTotal);

        // Check Tevkifat Logic (> 12000)
        checkAutoTevkifat(grandTotal);
    }

    function formatMoney(amount) {
        return amount.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺';
    }

    function toggleTevkifat() {
        const type = document.getElementById('fatura_tipi').value;
        const container = document.getElementById('tevkifat_container');
        if (type === 'TEVKIFAT') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function checkAutoTevkifat(grandTotal) {
        const typeSelect = document.getElementById('fatura_tipi');
        const tevkifatSelect = document.getElementById('tevkifat_kodu');

        // Only auto-switch if currently SATIS
        if (grandTotal >= 12000) {
            if (typeSelect.value === 'SATIS') {
                typeSelect.value = 'TEVKIFAT';
                toggleTevkifat();
                tevkifatSelect.value = '624'; // Default requested code

                Swal.fire({
                    icon: 'info',
                    text: 'Tutar 12.000 TL üzeri olduğu için Tevkifatlı Fatura (624) olarak ayarlandı.',
                    toast: true, position: 'bottom-end', timer: 4000, showConfirmButton: false
                });
            }
        }
    }

    // Reuse existing VKN/Title search functions...
    // Only keeping them for context, logic remains same.

    // VKN Sorgula
    function sorgulaVKN() {
        const vkn = document.getElementById('vkn').value;
        const btn = document.querySelector('button[onclick="sorgulaVKN()"]');

        if (!vkn || vkn.length < 10) return;

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        const formData = new FormData();
        formData.append('vkn', vkn);

        fetch('/api/vkn/sorgula', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-search"></i>';
                }

                if (data.success && data.mukellef) {
                    document.getElementById('unvan').value = data.mukellef.unvan || '';
                    if (data.mukellef.adres) document.getElementById('adres').value = data.mukellef.adres; // If available

                    updateBadge(data.mukellef.type, data.mukellef.alias);
                    Swal.fire({
                        icon: 'success', title: 'Mükellef Bulundu',
                        text: `${data.mukellef.unvan}`, timer: 1500, showConfirmButton: false,
                        toast: true, position: 'top-end'
                    });
                } else {
                    document.getElementById('alici_bilgi').innerHTML = '';
                }
            })
            .catch(err => {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search"></i>'; }
            });
    }

    // Inline Title Search Logic
    let searchTimeout;
    const suggestionsBox = document.getElementById('title_suggestions');

    function handleTitleInput(query) {
        clearTimeout(searchTimeout);
        if (query.length < 3) {
            suggestionsBox.classList.add('hidden');
            return;
        }

        document.getElementById('title_loading').classList.remove('hidden');

        searchTimeout = setTimeout(() => {
            const formData = new FormData();
            formData.append('q', query);

            fetch('/api/mukellef/ara', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('title_loading').classList.add('hidden');

                    if (data.success && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(user => {
                            if (!user) return; // Safety check

                            // Safe escape for onclick
                            const safeUnvan = user.unvan ? user.unvan.replace(/'/g, "\\'") : '';
                            const safeVkn = user.vkn || '';
                            const safeAlias = user.alias || '';
                            const safeType = user.type || 'UNKNOWN';

                            html += `
                            <div class="p-3 border-b border-white/5 hover:bg-white/10 cursor-pointer transition flex justify-between items-center group"
                                 onclick="selectSuggestion('${safeVkn}', '${safeUnvan}', '${safeAlias}', '${safeType}')">
                                <div>
                                    <div class="font-semibold text-sm group-hover:text-blue-400 transition">${user.unvan}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">VKN: ${user.vkn}</div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded bg-white/5 ${safeType === 'OZEL' ? 'text-purple-400' : 'text-blue-400'}">
                                    ${safeType}
                                </span>
                            </div>`;
                        });
                        suggestionsBox.innerHTML = html;
                        suggestionsBox.classList.remove('hidden');
                    } else {
                        suggestionsBox.classList.add('hidden');
                    }
                })
                .catch(() => {
                    suggestionsBox.classList.add('hidden');
                    document.getElementById('title_loading').classList.add('hidden');
                });
        }, 500);
    }

    function selectSuggestion(vkn, unvan, alias, type) {
        document.getElementById('vkn').value = vkn;
        document.getElementById('unvan').value = unvan;
        suggestionsBox.classList.add('hidden');
        updateBadge(type, alias);
    }

    function updateBadge(type, alias) {
        const typeBadge = document.getElementById('alici_bilgi');
        if (!typeBadge) return;

        const typeClass = type === 'OZEL' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400';
        typeBadge.innerHTML = `
            <div class="flex items-start gap-3 p-3 bg-white/5 rounded-xl border border-white/5">
                <div class="${type === 'OZEL' ? 'bg-purple-500' : 'bg-blue-500'} w-2 h-2 rounded-full mt-2"></div>
                <div>
                    <h4 class="text-sm font-semibold text-white">E-Fatura Mükellefi</h4>
                    <p class="text-xs text-gray-400 mt-1">Alias: ${alias || 'Varsayılan'}</p>
                    <span class="inline-block mt-2 text-[10px] uppercase tracking-wider ${type === 'OZEL' ? 'text-purple-400' : 'text-blue-400'}">
                        ${type} SENARYO
                    </span>
                </div>
            </div>`;
    }

    document.addEventListener('click', function (e) {
        if (!document.getElementById('unvan').contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });

    function validateForm() {
        // Basic validation
        const vkn = document.getElementById('vkn').value;
        if (vkn.length < 10) {
            Swal.fire('Hata', 'Geçerli bir VKN/TCKN giriniz.', 'error');
            return false;
        }
        return true;
    }
</script>