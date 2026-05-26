@extends('install._layout')

@section('content')
<div class="card-head">
    <h1><i class="fa-solid fa-database" style="color:#2563eb;font-size:20px;margin-right:6px"></i> Veritabanı Bağlantısı</h1>
    <p>MySQL/MariaDB önerilir. SQLite seçersen tek dosyalık portable çalışma elde edersin (küçük projeler için).</p>
</div>

@if($error)
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div><strong>Bağlantı başarısız:</strong> {{ $error }}</div>
    </div>
@endif

<form method="POST" action="/install/database" autocomplete="off">
    <div class="field">
        <label>Sürücü</label>
        <select name="driver" id="driver" onchange="document.querySelectorAll('.if-mysql').forEach(e=>e.style.display=this.value==='mysql'?'':'none');document.querySelectorAll('.if-sqlite').forEach(e=>e.style.display=this.value==='sqlite'?'':'none')">
            <option value="mysql" {{ ($form['driver'] ?? 'mysql') === 'mysql' ? 'selected' : '' }}>MySQL / MariaDB (önerilen)</option>
            <option value="sqlite" {{ ($form['driver'] ?? '') === 'sqlite' ? 'selected' : '' }}>SQLite (tek dosya)</option>
        </select>
    </div>

    <div class="if-mysql" style="{{ ($form['driver'] ?? 'mysql') === 'mysql' ? '' : 'display:none' }}">
        <div class="row">
            <div class="field">
                <label>Host</label>
                <input type="text" name="host" value="{{ $form['host'] ?? '127.0.0.1' }}" placeholder="127.0.0.1">
            </div>
            <div class="field">
                <label>Port</label>
                <input type="number" name="port" value="{{ $form['port'] ?? 3306 }}">
            </div>
        </div>
        <div class="field">
            <label>Veritabanı adı</label>
            <input type="text" name="database" value="{{ $form['database'] ?? '' }}" placeholder="ief_framework">
            <div class="hint">Önceden cPanel/Plesk'ten boş bir DB oluşturmuş olmalısın.</div>
        </div>
        <div class="row">
            <div class="field">
                <label>Kullanıcı</label>
                <input type="text" name="username" value="{{ $form['username'] ?? '' }}">
            </div>
            <div class="field">
                <label>Şifre</label>
                <input type="password" name="password" value="{{ $form['password'] ?? '' }}">
            </div>
        </div>
    </div>

    <div class="if-sqlite" style="{{ ($form['driver'] ?? '') === 'sqlite' ? '' : 'display:none' }}">
        <div class="field">
            <label>SQLite dosya yolu</label>
            <input type="text" name="path" value="{{ $form['path'] ?? '' }}" placeholder="/path/to/storage/db.sqlite">
            <div class="hint">Boş bırakırsan <code>storage/db.sqlite</code> kullanılır.</div>
        </div>
    </div>

    <div class="actions">
        <a href="/install" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Geri</a>
        <button type="submit" class="btn btn-primary">
            Bağlantıyı Test Et & Devam <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
