@extends('layouts.app')

@section('title', 'Randevu Al')

@section('content')

<section class="relative pt-24 pb-12 px-6 lg:px-8 border-b border-slate-100">
    <div class="absolute inset-0 -z-10 [background-image:linear-gradient(to_right,rgb(15_23_42_/_0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42_/_0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_70%)]"></div>
    <div class="max-w-3xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-5 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-700 shadow-soft">
            <i class="fa-solid fa-calendar-check text-brand-700 text-[11px]"></i> Randevu
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter leading-tight mb-4">
            Sana <span class="bg-gradient-to-r from-brand-900 via-brand-600 to-accent-500 bg-clip-text text-transparent">uygun zamanı</span> seç.
        </h1>
        <p class="text-base text-slate-600 max-w-xl mx-auto">Servisi ve dilediğin tarih/saati seç, biz onaylayıp dönüş yapacağız.</p>
    </div>
</section>

<section class="py-16 px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        @if(!$services)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
                <i class="fa-solid fa-circle-info text-2xl text-amber-600 mb-3"></i>
                <p class="text-sm text-amber-900">Şu an aktif bir servis tanımlanmamış. Lütfen daha sonra tekrar dene.</p>
            </div>
        @else
            <form method="post" action="/randevu" id="apptForm" class="bg-white border border-slate-200 rounded-2xl p-7 md:p-8 shadow-soft space-y-6">
                @csrf

                {{-- Servis seçimi --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-3">Servis seçin <span class="text-red-500">*</span></label>
                    <div class="grid sm:grid-cols-3 gap-3">
                        @foreach($services as $s)
                            <label class="block cursor-pointer">
                                <input type="radio" name="service_id" value="{{ $s->id }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }} required>
                                <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-brand-600 peer-checked:bg-brand-50/30 hover:border-slate-400 transition">
                                    <div class="text-sm font-bold text-slate-900">{{ $s->name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1"><i class="fa-regular fa-clock text-[10px]"></i> {{ $s->duration_min }} dk</div>
                                    @if($s->description)
                                        <div class="text-[11.5px] text-slate-500 mt-2 leading-snug">{{ str_limit($s->description, 60) }}</div>
                                    @endif
                                    @if((float) $s->price > 0)
                                        <div class="mt-3 text-[13px] font-bold text-brand-700">{{ format_money($s->price) }}</div>
                                    @else
                                        <div class="mt-3 text-[11px] font-semibold text-green-700 uppercase tracking-widest">Ücretsiz</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Tarih + Saat --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tarih <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $minDate }}"
                               class="w-full px-3.5 py-3 border border-slate-300 rounded-lg text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Saat <span class="text-red-500">*</span></label>
                        <select name="time" required class="w-full px-3.5 py-3 border border-slate-300 rounded-lg text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                            @for($h = 9; $h <= 17; $h++)
                                @foreach(['00','30'] as $m)
                                    <option value="{{ sprintf('%02d:%s',$h,$m) }}">{{ sprintf('%02d:%s',$h,$m) }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Müşteri --}}
                <div class="grid sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Ad Soyad <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">E-posta <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Telefon</label>
                    <input type="tel" name="phone" placeholder="(opsiyonel)" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Not</label>
                    <textarea name="notes" rows="3" placeholder="Görüşmek istediğin konu, sorular vs. (opsiyonel)" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-md text-[14px] focus:outline-none focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10 transition resize-y"></textarea>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-slate-900 hover:bg-black text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-glow transition-all">
                    <i class="fa-solid fa-calendar-check"></i> Randevu Talep Et
                </button>
                <p class="text-[12px] text-slate-400 text-center"><i class="fa-solid fa-shield-halved text-[10px] mr-1"></i> Talebin onaylandıktan sonra e-posta ile bilgilendirileceksin.</p>
            </form>
        @endif
    </div>
</section>

@endsection
