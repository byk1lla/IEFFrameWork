@extends('install._layout')

@section('content')
<div class="card-head">
    <h1><i class="fa-solid fa-arrow-down-up-across-line" style="color:#2563eb;font-size:18px;margin-right:6px"></i> Veritabanı Tablolarını Kur</h1>
    <p>16+ tablo (users, messages, posts, settings, traffic, ...) oluşturulacak. Bu işlem genelde 1-3 saniye sürer.</p>
</div>

@if($result && !$result['ok'])
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>
            <strong>Migrasyon başarısız:</strong> {{ $result['error'] }}
            <br><small>DB kullanıcısının <code>CREATE</code>, <code>ALTER</code>, <code>INDEX</code> yetkisi olduğundan emin ol.</small>
        </div>
    </div>
@endif

@if($result && $result['ok'])
    <div class="alert alert-ok">
        <i class="fa-solid fa-circle-check"></i>
        <div><strong>Tüm tablolar oluşturuldu.</strong></div>
    </div>
    @if(!empty($result['output']))
        <pre class="terminal">{{ $result['output'] }}</pre>
    @endif
@endif

<form method="POST" action="/install/migrate">
    <p style="font-size:14px;color:#475569;line-height:1.6">
        Hazır olduğunda butona tıkla — migrasyon çalışacak.
    </p>
    <div class="actions">
        <a href="/install/database" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Geri</a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-play"></i> Migrasyonu Başlat
        </button>
    </div>
</form>
@endsection
