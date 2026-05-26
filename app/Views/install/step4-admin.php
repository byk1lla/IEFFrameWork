@extends('install._layout')

@section('content')
<div class="card-head">
    <h1><i class="fa-solid fa-user-shield" style="color:#2563eb;font-size:18px;margin-right:6px"></i> İlk Yönetici Hesabı</h1>
    <p>Bu hesap <code>superadmin</code> rolüyle oluşturulur — her şeye erişimi olacak.</p>
</div>

@if($error)
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>{{ $error }}</div>
    </div>
@endif

<form method="POST" action="/install/admin" autocomplete="off">
    <div class="field">
        <label>İsim</label>
        <input type="text" name="name" required autocomplete="off" placeholder="Adın Soyadın">
    </div>
    <div class="field">
        <label>E-posta</label>
        <input type="email" name="email" required autocomplete="off" placeholder="admin@example.com">
        <div class="hint">Giriş yaparken bu e-postayı kullanacaksın.</div>
    </div>
    <div class="row">
        <div class="field">
            <label>Şifre</label>
            <input type="password" name="password" required minlength="8" autocomplete="new-password">
            <div class="hint">Min 8 karakter.</div>
        </div>
        <div class="field">
            <label>Şifre (tekrar)</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password">
        </div>
    </div>

    <div class="alert alert-info" style="margin-top:8px">
        <i class="fa-solid fa-shield-halved"></i>
        <div>
            Şifre <strong>bcrypt</strong> ile hash'lenir; veritabanında düz metin saklanmaz.
        </div>
    </div>

    <div class="actions">
        <a href="/install/migrate" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Geri</a>
        <button type="submit" class="btn btn-primary">
            Hesabı Oluştur <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
