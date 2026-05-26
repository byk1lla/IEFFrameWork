@extends('layouts.app')

@section('title', 'İletişim')

@section('content')

{{-- ═══ Hero ═══════════════════════════════════════════════════════ --}}
<section class="relative pt-24 pb-12 px-6 lg:px-8 border-b border-slate-100">
    <div class="absolute inset-0 -z-10 [background-image:linear-gradient(to_right,rgb(15_23_42_/_0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42_/_0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_70%)]"></div>
    <div class="max-w-3xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-5 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-700 shadow-soft">
            <i class="fa-solid fa-envelope text-brand-700 text-[11px]"></i>
            İletişim
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter leading-tight mb-4">
            Birlikte <span class="bg-gradient-to-r from-brand-900 via-brand-600 to-accent-500 bg-clip-text text-transparent">çalışalım.</span>
        </h1>
        <p class="text-base text-slate-600 max-w-xl mx-auto">Sorularını, geri bildirimini veya iş birliği taleplerini buradan iletebilirsin. Genelde 24 saat içinde dönüş yapıyoruz.</p>
    </div>
</section>

{{-- ═══ Form + Info ═══════════════════════════════════════════════ --}}
<section class="py-16 px-6 lg:px-8">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-[1fr_400px] gap-10">

        {{-- FORM --}}
        <div>
            @php
                $errors = flash('contact_errors') ?? [];
                $old    = flash('_old_input') ?? [];
            @endphp

            @if($errors)
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                    <div class="font-semibold mb-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> Formda hata var:</div>
                    <ul class="ml-5 list-disc space-y-0.5">
                        @foreach($errors as $field => $msgs)
                            @foreach((array)$msgs as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="/iletisim" class="bg-white border border-slate-200 rounded-2xl p-7 md:p-8 shadow-soft space-y-5" id="contactForm">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Ad Soyad <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
                            <input type="text" name="name" required value="{{ $old['name'] ?? '' }}" placeholder="Adınız ve soyadınız"
                                   class="w-full pl-10 pr-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">E-posta <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
                            <input type="email" name="email" required value="{{ $old['email'] ?? '' }}" placeholder="ornek@email.com"
                                   class="w-full pl-10 pr-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-[1fr_2fr] gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Telefon</label>
                        <div class="relative">
                            <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
                            <input type="tel" name="phone" value="{{ $old['phone'] ?? '' }}" placeholder="(opsiyonel)"
                                   class="w-full pl-10 pr-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Konu</label>
                        <input type="text" name="subject" value="{{ $old['subject'] ?? '' }}" placeholder="Mesajınızın konusu"
                               class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Mesaj <span class="text-red-500">*</span></label>
                    <textarea name="body" rows="6" required placeholder="Bize iletmek istediklerinizi yazın..."
                              class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[14px] text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y">{{ $old['body'] ?? '' }}</textarea>
                    <p class="text-[11.5px] text-slate-400 mt-1.5"><i class="fa-solid fa-shield-halved text-[10px] mr-1"></i> Bilgileriniz güvenle saklanır, üçüncü taraflarla paylaşılmaz.</p>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-glow transition-all">
                    <i class="fa-solid fa-paper-plane"></i>
                    Mesajı Gönder
                </button>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4">
            @php
                $email   = \App\Models\Setting::get('general.contact_email', config('app.contact_to'));
                $phone   = \App\Models\Setting::get('general.contact_phone');
                $address = \App\Models\Setting::get('general.address');
            @endphp

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-soft">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Bize ulaşın</h3>
                <ul class="space-y-4">
                    @if($email)
                        <li class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-[14px]"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] uppercase tracking-widest text-slate-400 font-bold">E-posta</div>
                                <a href="mailto:{{ $email }}" class="text-sm font-semibold text-slate-900 hover:text-brand-700 break-all">{{ $email }}</a>
                            </div>
                        </li>
                    @endif
                    @if($phone)
                        <li class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-[13px]"></i>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-widest text-slate-400 font-bold">Telefon</div>
                                <a href="tel:{{ preg_replace('/[^0-9+]/','',$phone) }}" class="text-sm font-semibold text-slate-900 hover:text-brand-700">{{ format_phone($phone) }}</a>
                            </div>
                        </li>
                    @endif
                    @if($address)
                        <li class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-[14px]"></i>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-widest text-slate-400 font-bold">Adres</div>
                                <div class="text-sm text-slate-700 leading-relaxed">{{ $address }}</div>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="bg-gradient-to-br from-brand-900 to-brand-700 text-white rounded-2xl p-6">
                <i class="fa-solid fa-clock text-2xl text-white/60 mb-3"></i>
                <h3 class="text-base font-bold mb-1.5">Yanıt süresi</h3>
                <p class="text-sm text-white/75 leading-relaxed">Mesajınızı genelde 24 saat içinde yanıtlıyoruz. Hafta sonu biraz daha uzun sürebilir.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-soft">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Sosyal</h3>
                <div class="flex gap-2">
                    <a href="https://github.com/byk1lla/IEFFrameWork" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 bg-slate-900 hover:bg-black text-white rounded-lg text-sm font-semibold transition">
                        <i class="fa-brands fa-github"></i> GitHub
                    </a>
                    <a href="https://iefsoftware.tr" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 bg-white border border-slate-300 hover:border-slate-900 text-slate-900 rounded-lg text-sm font-semibold transition">
                        <i class="fa-solid fa-globe"></i> Web
                    </a>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection

@section('scripts')
<script>
// SweetAlert ile gönderim öncesi mini doğrulama
document.getElementById('contactForm')?.addEventListener('submit', function(e){
    const body = this.querySelector('textarea[name=body]').value.trim();
    if (body.length < 5) {
        e.preventDefault();
        Swal.fire({
            icon:'warning', title:'Mesajınız çok kısa',
            text:'En az 5 karakter yazmanız gerekiyor.',
            confirmButtonColor:'#1e40af',
        });
    }
});
</script>
@endsection
