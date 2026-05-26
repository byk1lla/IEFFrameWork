@extends('install._layout')

@section('content')
<div class="card-head">
    <h1><i class="fa-solid fa-globe" style="color:#2563eb;font-size:18px;margin-right:6px"></i> Site Bilgileri</h1>
    <p>Bu adım opsiyonel — sonradan <strong>Admin > Ayarlar > Genel</strong>'den de düzenleyebilirsin.</p>
</div>

<form method="POST" action="/install/site">
    <div class="field">
        <label>Site Adı</label>
        <input type="text" name="site_name" value="IEF Framework" placeholder="Şirket / Site Adı">
    </div>
    <div class="field">
        <label>Slogan</label>
        <input type="text" name="site_tagline" placeholder="Tek satırlık tanıtım metni">
    </div>
    <div class="row">
        <div class="field">
            <label>İletişim E-posta</label>
            <input type="email" name="contact_email" placeholder="info@example.com">
        </div>
        <div class="field">
            <label>Telefon</label>
            <input type="text" name="contact_phone" placeholder="+90 555 111 22 33">
        </div>
    </div>
    <div class="row">
        <div class="field">
            <label>Zaman Dilimi</label>
            <select name="timezone">
                @foreach(['Europe/Istanbul','UTC','Europe/London','Europe/Berlin','America/New_York','Asia/Dubai'] as $tz)
                    <option value="{{ $tz }}" {{ $tz === 'Europe/Istanbul' ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Dil</label>
            <select name="locale">
                <option value="tr" selected>Türkçe</option>
                <option value="en">English</option>
            </select>
        </div>
    </div>

    <div class="actions">
        <a href="/install/done" class="btn btn-ghost">Atla</a>
        <button type="submit" class="btn btn-primary">
            Kaydet & Bitir <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
