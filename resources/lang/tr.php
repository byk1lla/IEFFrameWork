<?php

return [
    'hero_title' => 'Rakipsiz <br>:simplicity',
    'hero_power' => 'Güç.',
    'hero_subtitle' => 'Performans ve hassasiyetin mutlak zirvesini deneyimleyin. Minimalist mükemmeliyet ve saf hızın değerini bilen elitler için tasarlanmış bir PHP platformu.',
    'start_building' => 'Sistemi Başlat',
    'explore_blog' => 'Blogu Keşfet',
    'zen_engine' => 'Titan Motoru',
    'zen_engine_desc' => 'Arka plana çekilen, geriye sadece saf mantık ve güç bırakan bir template motoru.',
    'nexus_orm' => 'Obsidian ORM',
    'nexus_orm_desc' => 'Doğal bir dil gibi hissettiren akıcı, nesne yönelimli veritabanı etkileşimleri.',
    'nexus_control' => 'Kontrol Titanı',
    'nexus_control_desc' => 'Nihai yönetici kokpiti. Altyapınız üzerinde mutlak hakimiyet için yeniden tasarlandı.',
    'nexus_gateway' => 'Nexus Geçidi',
    'back_to_nexus' => 'Nexus\'a Dön',
    'knowledge' => 'Bilgi Birikimi',
    'ecosystem' => 'Ekosistem',
    'experience' => 'Deneyim',
    'admin_dashboard' => 'Titan Paneli',
    'status_ready' => 'Durum: Hazır',
    'control_center' => 'Kontrol Merkezi',
    'administrator' => 'Titan Gözetmeni',

    // V4 Additional Keys
    'blog_title' => 'TitanBlog',
    'blog_subtitle' => 'Obsidian Matrisi İçinden. Elit Mimari Üzerine Düşünceler.',
    'read_more' => 'Görev Günlüğünü Oku',
    'back_to_blog' => 'Görevi İptal Et (Bloga Dön)',
    'mission_by' => 'GÖREVİ BAŞLATAN:',
    'contact_title' => 'Titan:span',
    'contact_subtitle' => 'Sistem yöneticileriyle güvenli bir bağlantı kurun.',
    'contact_name_label' => 'Kimlik Doğrulama (İsim)',
    'contact_email_label' => 'Matrix Bağlantısı (E-posta)',
    'contact_message_label' => 'Derin Mesaj',
    'contact_submit' => 'Veriyi İlet',
    'contact_success' => 'İletişim Başarıyla Kuruldu.',
    'system_status' => 'SİSTEM DURUMU: %100 TİTANYUM',
    'blog_desc_placeholder' => 'İçerik hazırlanıyor...',
    'platform_badge' => 'IEF PLATFORM V4 • OBSIDIAN ÇEKİRDEK',
    'engine_core_label' => 'TİTAN MOTOR ÇEKİRDEĞİ',
    'return_to_zero' => 'Sıfıra Dön',
    'foundation' => 'Temel Katman',
    'mechanics' => 'Çekirdek Mekanikler',
    'visualization' => 'Görselleştirme',
    'intelligence' => 'Zeka',
    'knowledge_base_footer' => 'Titan V4 Bilgi Birikimi • Final Grade Clearance Gerekli • 2026',
    'philosophy_subtitle' => 'Performans ve hassasiyetin mutlak zirvesini deneyimleyin.',

    // YAŞAM DÖNGÜSÜ (Kuantum Detay)
    'doc_lifecycle' => 'Sistem Yürütme Döngüsü',
    'doc_lifecycle_desc' => 'Titan V4\'ün önyükleme süreci deterministik bir dizidir. Web sunucusunun `public/index.php` dosyasını tetiklemesiyle başlar ve `App` örneği uyandırılır.',
    'doc_lifecycle_singleton' => '`App` sınıfı Katı Singleton (Strict Singleton) desenini kullanır. `run()` sırasında sırasıyla Session başlatılır, yapılandırmalar yüklenir, Dil (Locale) tespit edilir ve Router çalışma ortamına bağlanır.',
    'doc_insight_lifecycle_50' => 'Yerelleştirme oturum bazlıdır. `Lang::load()` Session içindeki `locale` anahtarına bakar; yoksa varsayılan TR yüklenir ancak `/lang/{locale}` rotasıyla veri kaybı olmadan anında değiştirilebilir.',
    'doc_insight_lifecycle_54' => '`Router::dispatch()` çağrısı "Geri Dönüşü Olmayan Nokta"dır. Uygulamanın hangi parçasının çalıştırılacağını belirlemek için global sunucu değişkenlerini yakalar.',

    // OMURGA (Kuantum Detay)
    'doc_backbone' => 'Yönlendirme Matrisi',
    'doc_backbone_desc' => 'IEF\'de yönlendirme sadece yol eşleştirme değildir; karmaşık bir Regex yorumlama sistemidir. Method Overriding desteği ile tarayıcıların PUT ve DELETE göndermesine olanak tanır.',
    'doc_backbone_regex' => '`{id}` gibi değişkenler `preg_match` ile yakalanır. Sistem, PHP Reflection kullanarak bu parametreleri sıfır yapılandırma ile doğrudan Controller metodlarınıza enjekte eder.',
    'doc_insight_router_54_title' => 'Method Overriding Mantığı',
    'doc_insight_router_54' => 'Eğer bir POST isteği `_method` alanı (veya `X-HTTP-Method-Override` başlığı) içeriyorsa, Router çalışma anında isteği PUT veya DELETE olarak yeniden tanımlar.',

    // AYNA (Kuantum Detay)
    'doc_mirror' => 'Titan Direktif Motoru',
    'doc_mirror_desc' => 'Ayna (View) yorumlamaz; derler. `.php` dosyalarını tarar, IEF direktiflerini tespit eder ve bunları saf, optimize edilmiş PHP kodlarıyla değiştirir.',
    'doc_mirror_compiler' => 'Her `{{ }}` ifadesi `htmlspecialchars` ve `?? ""` boşluk operatörü ile sarmalanır. Bu, null bir değişken gönderilse bile arayüzün asla çökmemesini (Null-Safety) sağlar.',
    'doc_insight_mirror_safety_title' => 'Eval Bağlamı',
    'doc_insight_mirror_safety' => 'Derlenen içerik `eval()` ile yürütülür. Yürütme öncesi tüm veriler `extract($data)` ile dizi anahtarlarından canlı değişkenlere dönüştürülür.',

    // KAYNAK (Kuantum Detay)
    'doc_source' => 'Obsidian ORM Mimarisi',
    'doc_source_desc' => 'Obsidian akıcı bir sorgu oluşturucudur (Fluent Query Builder). "Tembel İnşa" (Lazy Building) kullanır; sorgular bellekte hazırlanır ve sadece `get()` veya `first()` gibi sonlandırıcılar çağrıldığında çalışır.',
    'doc_insight_orm_fluid' => 'Her `where()` çağrısı dahili bir diziye ekleme yapar. SQL sorgusu, maksimum performans için sadece son milisaniyede inşa edilir.',
    'doc_insight_orm_uuid_title' => 'UUID Üretim Mantığı',
    'doc_insight_orm_uuid' => 'Eğer `$useUuid` aktifse, `Model::create()` metodu veritabanına INSERT komutu gitmeden önce `Symfony/Uid` kullanarak RFC 4122 uyumlu bir UUID oluşturur.',

    // GEÇİT (Kuantum Detay)
    'doc_gateway' => 'Controller ve Protokol Mantığı',
    'doc_gateway_desc' => 'Controller\'lar sadece mantık havuzu değil, korumalı düğümlerdir. Temel sınıftan türetilerek basitleştirilmiş Response ve Validation sarmalayıcılarına erişim kazanırlar.',
    'doc_insight_controller_24_title' => 'Bağımlılık Enjeksiyonu',
    'doc_insight_controller_24' => 'Router metod parametrelerinizi analiz eder. Eğer bir `Request` tip belirteci görürse, Reflection üzerinden singleton Request örneğini otomatik olarak enjekte eder.',

    // DÜĞÜMLER
    'doc_nodes' => 'Sistem Düğüm Matrisi',
    'doc_nodes_desc' => 'Sistem dengesini sağlayan diğer hayati organlar:',
    'doc_node_req_title' => 'İstek Keşfi',
    'doc_node_req_desc' => 'JSON veri paketlerini otomatik algılar ve girdi akışına dahil eder. HTMX protokol desteği çekirdekten gelir.',
    'doc_node_sess_title' => 'Oturum Entropisi',
    'doc_node_sess_desc' => 'CSRF tokenlarını ve flaş mesajları yönetir. Tokenlar, replay saldırılarını önlemek için her durum değiştiren istekte yenilenir.',
    'doc_node_log_title' => 'Log Bütünlüğü',
    'doc_node_log_desc' => 'Atomik log dosyaları yazar. DEBUG, INFO, ERROR seviyelerini destekler. Günlük bazda saklanır.',

    // UI ETİKETLERİ
    'doc_singleton_title' => 'Singleton Yürütme',
    'doc_regex_title' => 'Regex Parametre Yakalama',
    'doc_compiler_title' => 'Direktif Derleme Hattı',
    'doc_fluid_api' => 'Akıcı Arayüz',
    'doc_insight_lang_50_title' => 'Anlık Dil Değişimi',
    'doc_insight_label' => 'Teknik İnceleme',
];
