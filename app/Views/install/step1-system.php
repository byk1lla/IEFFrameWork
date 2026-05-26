@extends('install._layout')

@section('content')
<div class="card-head">
    <h1>🚀 IEF Framework Kurulumu</h1>
    <p>Önce sistemin gereksinimleri karşılayıp karşılamadığını kontrol ediyoruz. Tüm satırlar yeşil olmalı.</p>
</div>

<ul class="checklist">
    @foreach($checks as $c)
        <li class="{{ $c['ok'] ? 'ok' : 'fail' }}">
            <div class="left">
                <span class="icon {{ $c['ok'] ? 'ok' : 'fail' }}">
                    <i class="fa-solid {{ $c['ok'] ? 'fa-check' : 'fa-xmark' }}"></i>
                </span>
                <span>{{ $c['label'] }}</span>
            </div>
            <span class="value">{{ $c['value'] }}</span>
        </li>
    @endforeach
</ul>

@if(!$allOk)
    <div class="alert alert-warn" style="margin-top:18px">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Bazı kontroller başarısız.</strong> Kırmızı satırları çöz, sonra sayfayı yenile.
            <br><small>Yazma izni problemi için: <code>chmod 775 storage sessions public/uploads</code></small>
        </div>
    </div>
@endif

<div class="actions">
    <a href="javascript:location.reload()" class="btn btn-ghost">
        <i class="fa-solid fa-rotate"></i> Yenile
    </a>
    <a href="/install/database" class="btn btn-primary" {{ $allOk ? '' : 'style=opacity:.5;pointer-events:none' }}>
        Devam Et <i class="fa-solid fa-arrow-right"></i>
    </a>
</div>
@endsection
