@extends('layouts.app')

@section('title', 'IEF Framework')

@section('content')

{{-- ═══ HERO ═══════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden pt-28 pb-24 px-6 lg:px-8">
    {{-- Grid background --}}
    <div class="absolute inset-0 -z-10 [background-image:linear-gradient(to_right,rgb(15_23_42_/_0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42_/_0.04)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_70%)]"></div>
    {{-- Glow blobs --}}
    <div class="absolute top-0 left-1/4 -z-10 w-[600px] h-[600px] bg-accent-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 -z-10 w-[500px] h-[500px] bg-brand-700/10 rounded-full blur-3xl"></div>

    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-7 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-700 shadow-soft animate__animated animate__fadeInDown">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse-soft"></span>
            {!! editable('home.hero.badge', 'v' . config('app.version','2.0.0') . ' · kararlı sürüm', ['tag'=>'span']) !!}
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter leading-[1.05] mb-6 animate__animated animate__fadeInUp">
            {!! editable('home.hero.title_main', 'PHP için', ['tag'=>'span']) !!}
            {!! editable('home.hero.title_accent', 'sade ve hızlı', ['tag'=>'span', 'class'=>'bg-gradient-to-r from-brand-900 via-brand-600 to-accent-500 bg-clip-text text-transparent']) !!}<br>
            {!! editable('home.hero.title_sub', 'modern bir framework.', ['tag'=>'span']) !!}
        </h1>
        <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed mb-9 animate__animated animate__fadeInUp animate__delay-1s">
            {!! editable('home.hero.lead', 'Routing, ORM, template engine, kimlik doğrulama, migration ve CLI dahil — ihtiyacın olan her şey tek pakette. Karmaşık konfigürasyon yok, .env yok, sadece çalışan kod.', ['tag'=>'span']) !!}
        </p>

        <div class="flex flex-wrap gap-3 justify-center animate__animated animate__fadeInUp animate__delay-2s">
            <a href="/docs" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-brand-900 via-brand-600 to-accent-500 hover:from-brand-700 hover:to-accent-600 text-white font-semibold text-sm rounded-lg shadow-glow transition-all">
                <i class="fa-solid fa-book-open"></i> Dökümantasyonu İncele
            </a>
            <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-900 hover:bg-black text-white font-semibold text-sm rounded-lg shadow-sm transition-all">
                <i class="fa-brands fa-github"></i> GitHub
            </a>
            <a href="#quickstart" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:border-slate-900 border border-slate-300 text-slate-900 font-semibold text-sm rounded-lg transition">
                Hızlı Başlangıç <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>

        <div class="mt-14 pt-10 border-t border-slate-200 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
            @php $metas = [['PHP','8.1+'],['Lisans','MIT'],['Bağımlılık','1'],['Boot','&lt;5ms']]; @endphp
            @foreach($metas as $m)
                <div>
                    <div class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">{{ $m[0] }}</div>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">{!! $m[1] !!}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ FEATURES ═══════════════════════════════════════════════════ --}}
<section class="py-24 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-6 lg:px-8 text-center mb-14">
        <div class="inline-block px-3 py-1 mb-4 text-[11px] font-bold text-brand-700 bg-brand-50 rounded uppercase tracking-widest">Çekirdek</div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">İhtiyacın olan her şey, kutudan çıkıyor.</h2>
        <p class="text-base text-slate-600 max-w-xl mx-auto mt-4">Küçük bir blog'dan kurumsal panele kadar — aynı framework, aynı verim.</p>
    </div>

    @php $features = [
        ['fa-cubes','Saf MVC','Controller / Model / View ayrımı net. Sıfır sihir, tahmin edilebilir.'],
        ['fa-database','ORM + Query Builder','Fluent sorgular, static facade, hasMany ilişki desteği.'],
        ['fa-table-list','Schema Builder','Aynı blueprint hem MySQL hem SQLite üretir.'],
        ['fa-shield-halved','Kimlik Doğrulama','Session + CSRF + role bazlı yetki kutudan hazır.'],
        ['fa-code','Template Engine','Blade-benzeri sözdizimi: @@extends, @@section, @@if.'],
        ['fa-terminal','CLI','serve, migrate, make:* generators, route:list, db:seed.'],
        ['fa-bug','Debug Bar','Her istekte SQL sorguları, süreler, memory tek bakışta.'],
        ['fa-language','Çoklu Dil','resources/lang ile placeholder destekli çeviri.'],
        ['fa-check-double','Validator','required, email, min, max, unique, exists kuralları.'],
        ['fa-layer-group','Middleware','Route grupları, prefix ve middleware miras desteği.'],
        ['fa-triangle-exclamation','Hata Sayfaları','Debug\'da kaynak kodlu stack trace, prod\'da temiz hata.'],
        ['fa-gear','Sade Konfigürasyon','config/ klasörü altında PHP dosyaları — .env\'siz.'],
    ]; @endphp

    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid sm:grid-cols-2 md:grid-cols-3 gap-5">
        @foreach($features as $f)
            <div class="bg-white p-7 rounded-xl border border-slate-100 hover:border-brand-200 hover:shadow-soft transition-all group">
                <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-brand-50 to-blue-50 border border-brand-100 flex items-center justify-center text-brand-700 group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $f[0] }} text-[16px]"></i>
                </div>
                <h3 class="text-[15px] font-bold mt-4 mb-1.5 tracking-tight">{{ $f[1] }}</h3>
                <p class="text-[13.5px] text-slate-600 leading-relaxed">{{ $f[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ CODE PREVIEW ═══════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-6 lg:px-8 text-center mb-12">
        <div class="inline-block px-3 py-1 mb-4 text-[11px] font-bold text-brand-700 bg-brand-50 rounded uppercase tracking-widest">Kod Örneği</div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Tanıdık. Sade. Okunabilir.</h2>
        <p class="text-base text-slate-600 max-w-md mx-auto mt-4">Laravel veya Eloquent kullandıysan zaten biliyorsun.</p>
    </div>

    <div class="max-w-3xl mx-auto px-6">
        <div class="bg-slate-950 rounded-xl overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-5 py-3 bg-white/5 border-b border-white/10">
                <div class="flex gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
                </div>
                <span class="text-[12px] text-slate-500 font-mono">app/Controllers/PostController.php</span>
                <span class="w-12"></span>
            </div>
<pre class="px-6 py-5 text-[13.5px] text-slate-200 font-mono leading-relaxed overflow-x-auto"><span class="text-sky-300">use</span> App\Models\Post;

<span class="text-sky-300">class</span> <span class="text-violet-300">PostController</span> <span class="text-sky-300">extends</span> <span class="text-violet-300">Controller</span>
{
    <span class="text-sky-300">public function</span> <span class="text-violet-300">index</span>()
    {
        <span class="text-amber-300">$posts</span> = Post::<span class="text-violet-300">where</span>(<span class="text-green-300">'published'</span>, <span class="text-sky-300">true</span>)
            -&gt;<span class="text-violet-300">orderBy</span>(<span class="text-green-300">'created_at'</span>, <span class="text-green-300">'DESC'</span>)
            -&gt;<span class="text-violet-300">take</span>(<span class="text-green-300">10</span>)
            -&gt;<span class="text-violet-300">get</span>();

        <span class="text-sky-300">return</span> <span class="text-amber-300">$this</span>-&gt;<span class="text-violet-300">view</span>(<span class="text-green-300">'posts.index'</span>, [<span class="text-green-300">'posts'</span> =&gt; <span class="text-amber-300">$posts</span>]);
    }
}</pre>
        </div>
    </div>
</section>

{{-- ═══ QUICKSTART ═════════════════════════════════════════════════ --}}
<section id="quickstart" class="py-24 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-6 lg:px-8 text-center mb-14">
        <div class="inline-block px-3 py-1 mb-4 text-[11px] font-bold text-brand-700 bg-brand-50 rounded uppercase tracking-widest">Hızlı Başlangıç</div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Üç komutta sıfırdan ayakta.</h2>
    </div>

    <div class="max-w-5xl mx-auto px-6 lg:px-8 grid md:grid-cols-3 gap-4">
        @php $steps = [
            ['1','Klonla & yükle','Repo\'yu çek, Composer bağımlılıkları kur.', "git clone github.com/byk1lla/IEFFrameWork app\ncd app\ncomposer install"],
            ['2','Veritabanı','config/database.php düzenle, migrate.', "./ief migrate"],
            ['3','Sunucu','Geliştirme sunucusunu başlat.', "./ief serve"],
        ]; @endphp
        @foreach($steps as $s)
            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:border-slate-900 transition group">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center mb-4 group-hover:scale-110 transition">{{ $s[0] }}</div>
                <h4 class="text-[15px] font-bold tracking-tight mb-1">{{ $s[1] }}</h4>
                <p class="text-[13.5px] text-slate-600 mb-4">{{ $s[2] }}</p>
                <pre class="bg-slate-950 text-emerald-300 text-[12.5px] font-mono p-3 rounded-md overflow-x-auto whitespace-pre-wrap break-words">{{ $s[3] }}</pre>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ ROADMAP ════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-6 lg:px-8 text-center mb-14">
        <div class="inline-block px-3 py-1 mb-4 text-[11px] font-bold text-brand-700 bg-brand-50 rounded uppercase tracking-widest">Yol Haritası</div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Modüler genişleme.</h2>
        <p class="text-base text-slate-600 mt-4">Çekirdek hazır, modüller sırayla geliyor.</p>
    </div>

    <div class="max-w-5xl mx-auto px-6 lg:px-8 grid md:grid-cols-2 gap-5">
        @php $tiers = [
            ['Çekirdek', 'Tier 1', 'Her projede yer alan temeller.', [
                ['Routing & Middleware','ready'], ['ORM & Schema Builder','ready'], ['Template Engine','ready'],
                ['Kimlik Doğrulama','ready'], ['Validator','ready'], ['CLI','ready'], ['Debug Bar','ready'],
                ['İletişim Formu','ready'], ['Mail Servisi (SMTP)','ready'], ['Storage / Dosya Yükleme','ready'],
                ['Log Görüntüleyici','ready'], ['Blog Modülü','ready'], ['Galeri / Medya','ready'],
                ['Kullanıcı Yönetimi','ready'], ['Site Ayarları (Genel/Mail/SEO)','ready'],
                ['Trafik Loglama (in-house)','ready'], ['Admin Dashboard','ready'],
            ]],
            ['Opsiyonel', 'Tier 2', 'Sık ihtiyaç olan, paket olarak yüklenecek.', [
                ['Google Analytics (GA4 inject)','soon'], ['RBAC (Gelişmiş yetki)','soon'],
                ['Şifre Sıfırlama (mail token)','soon'], ['Site Editörü (Inline CMS)','todo'],
                ['WhatsApp Floating Button','todo'], ['SEO Yöneticisi (sitemap/robots)','soon'],
                ['PWA Üretici','todo'], ['DataTables Entegrasyonu','ready'],
                ['Randevu Sistemi','todo'],
            ]],
        ]; @endphp
        @foreach($tiers as $t)
            <div class="bg-white border border-slate-200 rounded-xl p-7 hover:shadow-card transition">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-bold tracking-tight">{{ $t[0] }}</h3>
                    <span class="text-[10px] font-bold tracking-widest text-brand-700 bg-brand-50 px-2 py-0.5 rounded">{{ $t[1] }}</span>
                </div>
                <p class="text-[13px] text-slate-500 mb-4">{{ $t[2] }}</p>
                <ul class="border-t border-slate-100">
                    @foreach($t[3] as $it)
                        <li class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-b-0 text-[13.5px] text-slate-700">
                            <span>{{ $it[0] }}</span>
                            @if($it[1] === 'ready')
                                <span class="text-[9.5px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-green-100 text-green-700">Hazır</span>
                            @elseif($it[1] === 'soon')
                                <span class="text-[9.5px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Geliyor</span>
                            @else
                                <span class="text-[9.5px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Planlı</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ CLI Table ══════════════════════════════════════════════════ --}}
<section class="py-24 border-t border-slate-200">
    <div class="max-w-3xl mx-auto px-6 lg:px-8 text-center mb-12">
        <div class="inline-block px-3 py-1 mb-4 text-[11px] font-bold text-brand-700 bg-brand-50 rounded uppercase tracking-widest">Komut Satırı</div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Tek CLI. Her şey.</h2>
    </div>
    <div class="max-w-3xl mx-auto px-6 lg:px-8 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-soft">
        @php $cmds = [
            ['./ief serve [port]','Geliştirme sunucusu (varsayılan :8000)'],
            ['./ief migrate','Bekleyen migration\'ları çalıştır'],
            ['./ief migrate:fresh','Tüm tabloları sil, sıfırdan migrate'],
            ['./ief make:controller <Name>','Yeni controller iskeleti'],
            ['./ief make:model <Name>','Yeni model iskeleti'],
            ['./ief make:migration <desc>','Yeni migration dosyası'],
            ['./ief make:middleware <Name>','Yeni middleware iskeleti'],
            ['./ief route:list','Tanımlı tüm route\'ları listele'],
            ['./ief cache:clear','storage/cache dizinini temizle'],
            ['./ief key:generate','Yeni uygulama anahtarı üret'],
        ]; @endphp
        @foreach($cmds as $c)
            <div class="grid md:grid-cols-[280px_1fr] gap-2 px-5 py-3 border-t border-slate-100 first:border-t-0 hover:bg-slate-50 transition">
                <code class="text-brand-800 font-mono text-[13px] font-semibold">{{ $c[0] }}</code>
                <span class="text-[13.5px] text-slate-600">{{ $c[1] }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ FINAL CTA ══════════════════════════════════════════════════ --}}
<section class="py-24 bg-gradient-to-br from-brand-900 via-brand-800 to-slate-900 text-white text-center">
    <div class="max-w-2xl mx-auto px-6">
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Hadi başla.</h2>
        <p class="text-white/70 text-base mb-8">Repo açık. Doc okunabilir. İlk projeni 15 dakikada ayağa kaldır.</p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-slate-900 font-bold text-sm rounded-lg hover:bg-slate-100 transition">
                <i class="fa-brands fa-github"></i> GitHub'da Görüntüle
            </a>
            <a href="#quickstart" class="inline-flex items-center gap-2 px-5 py-3 border border-white/20 hover:border-white text-white font-semibold text-sm rounded-lg transition">
                Kuruluma Bak
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    // Sayfa kayarken hafif fade-in efekti — fromTo, başlangıçta her şey görünür kalır.
    if (window.gsap && window.ScrollTrigger) {
        gsap.registerPlugin(ScrollTrigger);
        gsap.utils.toArray('section .grid > div, section h2').forEach(el => {
            gsap.fromTo(el,
                { y: 12, opacity: .85 },
                { y: 0, opacity: 1, duration: .5, ease: 'power2.out',
                  scrollTrigger: { trigger: el, start: 'top 92%', once: true } }
            );
        });
    }
</script>
@endsection
