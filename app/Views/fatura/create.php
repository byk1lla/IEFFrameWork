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

        <!-- Müşteri Seçimi -->
        <div class="glass rounded-2xl p-6 relative z-30">
            <div id="selection_header" class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-users text-blue-400"></i>
                    Müşteri Seçimi
                </h2>
                <button type="button" id="change_customer_btn" onclick="resetSelection()"
                    class="hidden text-xs bg-white/5 hover:bg-white/10 text-gray-400 px-3 py-1.5 rounded-lg transition">
                    <i class="fas fa-sync-alt mr-1"></i> Değiştir
                </button>
            </div>

            <!-- Arama Bölümü -->
            <div id="search_zone" class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-500"></i>
                    </div>
                    <input type="text" id="customer_search_input"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 pl-12 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 transition text-lg shadow-inner"
                        placeholder="Müşteri adı veya VKN yazarak arayın..." oninput="handleCustomerSearch(this.value)">

                    <div id="search_loading" class="hidden absolute right-6 top-[22px]">
                        <div class="small-ring-spinner"></div>
                    </div>
                </div>

                <div id="customer_suggestions"
                    class="hidden bg-gray-900/50 border border-white/10 rounded-2xl shadow-2xl overflow-hidden divide-y divide-white/5 z-50">
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <div class="h-px flex-1 bg-white/5"></div>
                    <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">veya</span>
                    <div class="h-px flex-1 bg-white/5"></div>
                </div>

                <button type="button" onclick="showRecipientForm(true)"
                    class="w-full py-4 bg-white/5 hover:bg-white/10 border border-dashed border-white/20 rounded-2xl text-gray-400 hover:text-white transition flex items-center justify-center gap-3 group">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="font-semibold text-sm">Yeni Müşteri Bilgilerini El ile Gir</span>
                </button>
            </div>

            <!-- Seçili Müşteri Özeti (Form açılınca üstte kalacak) -->
            <div id="selected_customer_summary" class="hidden animate-fade-in">
                <div class="flex items-center gap-4 p-4 bg-blue-500/10 rounded-2xl border border-blue-500/20">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white text-xl">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div id="summ_unvan" class="font-bold text-white truncate">Müşteri Ünvanı</div>
                        <div id="summ_vkn" class="text-xs text-blue-400 font-mono">3240232123</div>
                    </div>
                    <div id="summ_badge"></div>
                </div>
            </div>

            <!-- Gizli Alıcı Formu -->
            <div id="recipient_form" class="hidden mt-8 pt-8 border-t border-white/5 space-y-4 animate-fade-in">
                <div class="grid lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">VKN / TCKN</label>
                        <div class="flex gap-2">
                            <input type="text" name="vkn" id="vkn" required maxlength="11"
                                class="flex-1 bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                                placeholder="VKN giriniz">
                            <button type="button" id="vkn_sorgu_btn" onclick="sorgulaVKN()"
                                class="px-4 bg-blue-500 rounded-xl hover:bg-blue-600 transition flex items-center justify-center min-w-[50px] relative overflow-hidden group/vknbtn">
                                <i class="fas fa-search transition-transform group-hover/vknbtn:scale-125"></i>
                                <div id="vkn_loader"
                                    class="hidden absolute inset-0 bg-blue-600 flex items-center justify-center">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                    <style>
                        @keyframes spin {
                            to {
                                transform: rotate(360deg);
                            }
                        }

                        .animate-fade-in {
                            animation: fadeIn 0.3s ease-out forwards;
                        }

                        @keyframes fadeIn {
                            from {
                                opacity: 0;
                                transform: translateY(10px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                    </style>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Ünvan / Ad Soyad</label>
                        <input type="text" name="unvan" id="unvan" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500"
                            placeholder="Firma ünvanı">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Adres</label>
                    <textarea name="adres" id="adres" rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:border-blue-500 placeholder-gray-500"
                        placeholder="Tam adres giriniz"></textarea>
                </div>

                <div id="alici_bilgi"></div>
            </div>
        </div>

        <!-- Fatura Ayarları -->
        <div class="glass rounded-2xl p-6 relative z-20">
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
                        <option value="611">611 - TAŞIMACILIK HİZMETLERİ</option>
                        <option value="612">612 - SERVİS
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
        <div class="glass rounded-2xl p-6 relative z-10">
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
            <div
                class="flex-1 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-500 text-sm flex items-start gap-3">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                    <p class="font-bold mb-1">Bilgi: Sadece Taslak Oluşturulur</p>
                    <p class="text-xs opacity-80">Güvenliğiniz için bu uygulama üzerinden direkt fatura gönderimi
                        kapalıdır. Oluşturulan taslakları <b>EDM Bilişim Portalı</b> üzerinden kontrol edip
                        onaylayabilirsiniz.</p>
                </div>
            </div>
            <button type="submit" name="action" value="taslak"
                class="lg:w-72 py-4 bg-blue-600 rounded-xl font-bold hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
                <i class="fas fa-save"></i>
                TASLAK OLUŞTUR
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

</script>

<!-- Searchable Tevkifat Dropdown Logic & Default Service -->
<script>
    // Tevkifat Options Data
    const tevkifatCodes = [
        { code: '601', text: '601 - YAPIM İŞLERİ İLE BU İŞLERLE BİRLİKTE İFA EDİLEN MÜHENDİSLİK-MİMARLIK VE ETÜT-PROJE HİZMETLERİ' },
        { code: '602', text: '602 - ETÜT, PLAN-PROJE, DANIŞMANLIK, DENETİM VE BENZERİ HİZMETLER' },
        { code: '603', text: '603 - MAKİNE, TEÇHİZAT, DEMİRBAŞ VE TAŞITLARA AİT TADİL, BAKIM VE ONARIM HİZMETLERİ' },
        { code: '604', text: '604 - YEMEK SERVİS HİZMETİ' },
        { code: '605', text: '605 - ORGANİZASYON HİZMETİ' },
        { code: '606', text: '606 - İŞGÜCÜ TEMİN HİZMETLERİ' },
        { code: '607', text: '607 - ÖZEL GÜVENLİK HİZMETİ' },
        { code: '608', text: '608 - YAPI DENETİM HİZMETLERİ' },
        { code: '609', text: '609 - TURİSTİK MAĞAZALARA VERİLEN MÜŞTERİ BULMA / GÖTÜRME HİZMETLERİ' },
        { code: '610', text: '610 - SPOR KULÜPLERİNİN YAYIN, REKLAM VE İSİM HAKKI GELİRLERİNE KONU İŞLEMLERİ' },
        { code: '611', text: '611 - TAŞIMACILIK HİZMETLERİ' },
        { code: '612', text: '612 - SERVİS TAŞIMACILIĞI HİZMETİ' },
        { code: '613', text: '613 - BASILI KİTAP VE SÜRELİ YAYINLARIN TESLİMİ' },
        { code: '614', text: '614 - HER TÜRLÜ BASKI VE BASIM HİZMETLERİ' },
        { code: '615', text: '615 - HURDA VE ATIK TESLİMİ' },
        { code: '616', text: '616 - METAL, PLASTİK, LASTİK, KAUÇUK, KAĞIT, CAM HURDA VE ATIKLARDAN ELDE EDİLEN HAMMADDE TESLİMİ' },
        { code: '617', text: '617 - PAMUK, TİFTİK, YÜN VE YAPAĞI İLE HAM POST VE DERİ TESLİMLERİ' },
        { code: '618', text: '618 - AĞAÇ VE ORMAN ÜRÜNLERİ TESLİMİ' },
        { code: '619', text: '619 - ÇİNKO, ALÜMİNYUM VE KURŞUN KÜLÇE TESLİMİ' },
        { code: '620', text: '620 - BAKIR, ÇİNKO, ALÜMİNYUM VE KURŞUN ÜRÜNLERİNİN TESLİMİ' },
        { code: '621', text: '621 - İKİNCİ EL MOTORLU KARA TAŞITI TİCARETİYLE İŞTİGAL EDEN MÜKELLEFLERCE %18 KDV ORANI UYGULANARAK YAPILAN ARAÇ TESLİMLERİ' },
        { code: '622', text: '622 - DİĞER HİZMETLER' },
        { code: '623', text: '623 - DİĞER TESLİMLER' },
        { code: '624', text: '624 - YÜK TAŞIMACILIĞI HİZMETİ', selected: true },
        { code: '625', text: '625 - TİCARİ REKLAM HİZMETLERİ' },
        { code: '626', text: '626 - KÜLÇE METAL TESLİMLERİ' },
        { code: '627', text: '627 - DEMİR-ÇELİK ÜRÜNLERİNİN TESLİMİ' }
    ];

    let rowCount = 0;
    let defaultService = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch Default Service
        fetch('/api/hizmet/varsayilan')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    defaultService = data.service;
                }
                addRow(); // Add first row AFTER fetching default service (or trying to)
                calculateTotals();
            })
            .catch(() => {
                addRow(); // Fallback
                calculateTotals();
            });

        toggleTevkifat();
        initTevkifatDropdown();

        // Check URL Params for Pre-fill (From Mukellef Sorgula)
        const urlParams = new URLSearchParams(window.location.search);
        const preVkn = urlParams.get('vkn');
        const preUnvan = urlParams.get('unvan');
        const preAdres = urlParams.get('adres');
        const preAlias = urlParams.get('alias');

        if (preVkn && preUnvan) {
            // Use the new selectCustomer logic for clean transition
            selectCustomer(preVkn, preUnvan, preAlias, 'OZEL', preAdres, false);
        } else if (preVkn) {
            // Fallback for VKN-only prefill
            document.getElementById('vkn').value = preVkn;
            if (preUnvan) document.getElementById('unvan').value = preUnvan;
            showRecipientForm(true);
            sorgulaVKN();
        }
    });

    function addRow() {
        const template = document.getElementById('item-row-template');
        const container = document.getElementById('items-container');
        const clone = template.content.cloneNode(true);

        // Replace placeholders
        let html = clone.querySelector('div').outerHTML.replace(/{index}/g, rowCount++);

        // Insert HTML
        container.insertAdjacentHTML('beforeend', html);

        // If we have default service, fill the NEW row
        if (defaultService) {
            const newRow = container.lastElementChild;
            const nameInput = newRow.querySelector('[name*="[name]"]');
            const priceInput = newRow.querySelector('[name*="[price]"]');
            const taxInput = newRow.querySelector('[name*="[tax]"]');

            if (nameInput) nameInput.value = defaultService.name;
            if (priceInput) priceInput.value = defaultService.price;
            if (taxInput) taxInput.value = defaultService.tax;

            calculateTotals();
        }
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

    /* Custom Searchable Dropdown for Tevkifat */
    function initTevkifatDropdown() {
        const container = document.getElementById('tevkifat_container');
        // Hide original select but keep it for form submission
        const originalSelect = document.getElementById('tevkifat_kodu');
        originalSelect.classList.add('hidden');

        // Create Custom UI
        const customUI = document.createElement('div');
        customUI.className = 'relative group';
        customUI.innerHTML = `
            <div id="tk_trigger" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white flex justify-between items-center cursor-pointer hover:bg-white/10 transition" onclick="toggleTkDropdown()">
                <span id="tk_selected_text" class="truncate pr-4 text-sm">Seçiniz...</span>
                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" id="tk_arrow"></i>
            </div>
            
            <div id="tk_dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-[#1e293b] border border-white/10 rounded-xl shadow-2xl z-50 max-h-60 flex flex-col">
                <div class="p-2 border-b border-white/5 sticky top-0 bg-[#1e293b] rounded-t-xl">
                    <input type="text" id="tk_search" placeholder="Kodu veya adı ile ara..." 
                           class="w-full bg-black/20 border border-white/5 rounded-lg py-2 px-3 text-sm text-white focus:outline-none focus:border-blue-500"
                           oninput="filterTkOptions(this.value)">
                </div>
                <div id="tk_options" class="overflow-y-auto flex-1 p-1 space-y-1 custom-scrollbar">
                    <!-- Options injected here -->
                </div>
            </div>
        `;

        container.appendChild(customUI);

        // Populate Options
        const optionsList = document.getElementById('tk_options');
        tevkifatCodes.forEach(opt => {
            const div = document.createElement('div');
            div.className = 'p-2 rounded-lg hover:bg-white/5 cursor-pointer text-sm text-gray-300 hover:text-white transition';
            div.textContent = opt.text;
            div.dataset.code = opt.code;
            div.onclick = () => selectTkOption(opt.code, opt.text);
            optionsList.appendChild(div);

            if (opt.selected) selectTkOption(opt.code, opt.text); // Default selection
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!customUI.contains(e.target)) {
                document.getElementById('tk_dropdown').classList.add('hidden');
                document.getElementById('tk_arrow').classList.remove('rotate-180');
            }
        });
    }

    function toggleTkDropdown() {
        const dd = document.getElementById('tk_dropdown');
        const arrow = document.getElementById('tk_arrow');
        const search = document.getElementById('tk_search');

        dd.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');

        if (!dd.classList.contains('hidden')) {
            setTimeout(() => search.focus(), 100);
        }
    }

    function filterTkOptions(query) {
        const lowerQ = query.toLowerCase();
        const options = document.getElementById('tk_options').children;

        Array.from(options).forEach(opt => {
            const text = opt.textContent.toLowerCase();
            if (text.includes(lowerQ)) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function selectTkOption(code, text) {
        const originalSelect = document.getElementById('tevkifat_kodu');
        originalSelect.value = code; // Update hidden select

        document.getElementById('tk_selected_text').textContent = text;
        document.getElementById('tk_dropdown').classList.add('hidden');
        document.getElementById('tk_arrow').classList.remove('rotate-180');
    }

    function checkAutoTevkifat(grandTotal) {
        const typeSelect = document.getElementById('fatura_tipi');
        // Only switch if currently SATIS
        if (grandTotal >= 12000) {
            if (typeSelect.value === 'SATIS') {
                typeSelect.value = 'TEVKIFAT';
                toggleTevkifat();

                // Switch dropdown to 624
                const defaultCode = '624';
                const defaultOpt = tevkifatCodes.find(t => t.code === defaultCode);
                if (defaultOpt) selectTkOption(defaultCode, defaultOpt.text);

                Swal.fire({
                    icon: 'info',
                    text: 'Tutar 12.000 TL üzeri olduğu için Tevkifatlı Fatura (624) olarak ayarlandı.',
                    toast: true, position: 'bottom-end', timer: 4000, showConfirmButton: false
                });
            }
        }
    }

    /* Fixed Selection Logic */
    let searchTimeout;
    const searchZone = document.getElementById('search_zone');
    const recipientForm = document.getElementById('recipient_form');
    const selectedSummary = document.getElementById('selected_customer_summary');
    const changeBtn = document.getElementById('change_customer_btn');
    const custSuggestions = document.getElementById('customer_suggestions');
    const custSearchInput = document.getElementById('customer_search_input');

    function handleCustomerSearch(query) {
        clearTimeout(searchTimeout);
        if (query.length < 3) { custSuggestions.classList.add('hidden'); return; }

        document.getElementById('search_loading').classList.remove('hidden');
        searchTimeout = setTimeout(() => {
            const formData = new FormData();
            formData.append('q', query);
            fetch('/api/mukellef/ara', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('search_loading').classList.add('hidden');
                    if (data.success && data.data.length > 0) {
                        renderCustomerSuggestions(data.data);
                    } else {
                        custSuggestions.classList.add('hidden');
                    }
                })
                .catch(() => {
                    document.getElementById('search_loading').classList.add('hidden');
                    custSuggestions.classList.add('hidden');
                });
        }, 400);
    }

    function renderCustomerSuggestions(data) {
        const local = data.filter(u => u.is_local);
        const global = data.filter(u => !u.is_local);

        let html = '';

        if (local.length > 0) {
            html += `<div class="p-2 bg-blue-500/10 text-[10px] text-blue-400 font-bold uppercase tracking-widest border-b border-white/5">Mevcut Müşteriler</div>`;
            local.forEach(user => {
                html += renderSingleItem(user);
            });
        }

        if (global.length > 0) {
            html += `<div class="p-2 bg-purple-500/10 text-[10px] text-purple-400 font-bold uppercase tracking-widest border-b border-white/5">Sistem Genelinde Ara</div>`;
            global.forEach(user => {
                html += renderSingleItem(user);
            });
        }

        custSuggestions.innerHTML = html;
        custSuggestions.classList.remove('hidden');
    }

    function renderSingleItem(user) {
        const safeUnvan = user.unvan ? user.unvan.replace(/'/g, "\\'").replace(/\n/g, ' ') : '';
        const safeVkn = user.vkn || '';
        const safeAlias = user.alias || '';
        const safeType = user.type || 'UNKNOWN';
        const safeAdres = user.adres ? user.adres.replace(/'/g, "\\'").replace(/\n/g, ' ') : '';
        const isLocal = user.is_local ? true : false;

        return `
            <div class="p-4 hover:bg-white/5 cursor-pointer transition flex justify-between items-center group" 
                 onclick="selectCustomer('${safeVkn}', '${safeUnvan}', '${safeAlias}', '${safeType}', '${safeAdres}', ${isLocal})">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-gray-500 group-hover:bg-blue-500/20 group-hover:text-blue-400 transition font-bold">
                        ${user.unvan ? user.unvan.charAt(0).toUpperCase() : '?'}
                    </div>
                    <div>
                        <div class="font-bold text-white group-hover:text-blue-400 transition flex items-center gap-2">
                            ${user.unvan}
                            ${isLocal ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-500 font-bold border border-amber-500/20">REHBER</span>' : ''}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5 font-mono">${user.vkn} | ${safeType}</div>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-700 group-hover:text-blue-500 transition"></i>
            </div>
        `;
    }

    function selectCustomer(vkn, unvan, alias, type, adres, isLocal) {
        // Pre-fill hidden form
        document.getElementById('vkn').value = vkn;
        document.getElementById('unvan').value = unvan;
        document.getElementById('adres').value = adres || '';

        // Update Summary
        document.getElementById('summ_unvan').textContent = unvan;
        document.getElementById('summ_vkn').textContent = vkn;

        const badge = document.getElementById('summ_badge');
        badge.innerHTML = `<span class="text-[10px] px-2 py-1 rounded-lg font-bold ${type === 'OZEL' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400'}">${type}</span>`;

        // Switch View
        showRecipientForm(false);
        updateBadge(type, alias, isLocal);

        // Final UI Polish
        custSuggestions.classList.add('hidden');
        custSearchInput.value = '';
    }

    function showRecipientForm(isManual = false) {
        searchZone.classList.add('hidden');
        recipientForm.classList.remove('hidden');
        changeBtn.classList.remove('hidden');

        if (!isManual) {
            selectedSummary.classList.remove('hidden');
        } else {
            selectedSummary.classList.add('hidden');
            document.getElementById('vkn').value = '';
            document.getElementById('unvan').value = '';
            document.getElementById('adres').value = '';
            document.getElementById('alici_bilgi').innerHTML = '';
            document.getElementById('vkn').focus();
        }
    }

    function resetSelection() {
        searchZone.classList.remove('hidden');
        recipientForm.classList.add('hidden');
        selectedSummary.classList.add('hidden');
        changeBtn.classList.add('hidden');
        custSearchInput.focus();
    }

    function sorgulaVKN() {
        const vkn = document.getElementById('vkn').value;
        const btn = document.getElementById('vkn_sorgu_btn');
        const loader = document.getElementById('vkn_loader');

        if (!vkn || vkn.length < 10) return;

        btn.disabled = true;
        loader.classList.remove('hidden');
        btn.classList.add('animate-pulse');

        const formData = new FormData();
        formData.append('vkn', vkn);
        fetch('/api/vkn/sorgula', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                loader.classList.add('hidden');
                btn.classList.remove('animate-pulse');
                if (data.success && data.mukellef) {
                    document.getElementById('unvan').value = data.mukellef.unvan || '';
                    if (data.mukellef.adres) document.getElementById('adres').value = data.mukellef.adres;
                    updateBadge(data.mukellef.type, data.mukellef.alias, data.mukellef.is_local);

                    Swal.fire({
                        icon: 'success', title: 'Mükellef Bilgileri Geldi',
                        text: `${data.mukellef.unvan}`,
                        timer: 1500, showConfirmButton: false, toast: true, position: 'top-end'
                    });
                }
            })
            .catch(() => { btn.disabled = false; loader.classList.add('hidden'); });
    }

    function updateBadge(type, alias, isLocal = false) {
        const typeBadge = document.getElementById('alici_bilgi');
        if (!typeBadge) return;
        typeBadge.innerHTML = `
            <div class="flex items-start gap-4 p-4 bg-white/5 rounded-2xl border border-white/5 shadow-inner">
                <div class="w-2 h-2 rounded-full ${type === 'OZEL' ? 'bg-purple-500 shadow-[0_0_10px_#a855f7]' : 'bg-blue-500 shadow-[0_0_10px_#3b82f6]'} mt-1.5"></div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        E-FATURA SİSTEMİNDE KAYITLI
                        ${isLocal ? '<span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500 text-black font-black uppercase tracking-tighter">REHBER</span>' : ''}
                    </h4>
                    <p class="text-[11px] text-gray-500 mt-1">Sistem tarafından otomatik doğrulandı.</p>
                    <div class="flex gap-2 mt-3">
                        <span class="text-[10px] bg-white/5 px-2 py-1 rounded font-mono text-gray-400">ALIAS: ${alias || 'default'}</span>
                        <span class="text-[10px] bg-white/5 px-2 py-1 rounded font-mono text-gray-400 capitalize">TİP: ${type}</span>
                    </div>
                </div>
            </div>`;
    }

    function validateForm() {
        const vkn = document.getElementById('vkn').value;
        if (vkn.length < 10) { Swal.fire('Hata', 'Geçerli bir VKN/TCKN giriniz.', 'error'); return false; }
        return true;
    }

</script>