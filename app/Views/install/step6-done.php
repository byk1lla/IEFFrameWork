@extends('install._layout')

@section('content')
<div style="text-align:center;padding:20px 10px 10px">
    <div style="display:inline-flex;align-items:center;justify-content:center;width:88px;height:88px;border-radius:24px;background:linear-gradient(135deg,#16a34a,#06b6d4);color:#fff;font-size:36px;margin-bottom:20px;box-shadow:0 16px 40px -10px rgba(22,163,74,.4);animation:bounce 1s ease">
        <i class="fa-solid fa-check"></i>
    </div>
    <h1 style="font-size:30px;font-weight:800;letter-spacing:-.025em;color:#0f172a;margin-bottom:8px">
        Kurulum Tamamlandı 🎉
    </h1>
    <p style="font-size:15px;color:#64748b;line-height:1.6;max-width:480px;margin:0 auto 8px">
        IEF Framework başarıyla kuruldu. Artık admin panelinden yönetmeye başlayabilirsin.
    </p>
    <p style="font-size:12px;color:#94a3b8;margin-bottom:28px">
        <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font:500 11.5px 'JetBrains Mono'">storage/installed.lock</code> dosyası oluşturuldu — <code>/install</code> artık erişilemez.
    </p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin:24px 0">
    <a href="/login" class="btn btn-primary" style="padding:18px;flex-direction:column;gap:6px;align-items:center;height:auto">
        <i class="fa-solid fa-right-to-bracket" style="font-size:22px"></i>
        <span>Yönetime Giriş Yap</span>
        <small style="font-weight:400;opacity:.85">/login</small>
    </a>
    <a href="/" class="btn btn-ghost" style="padding:18px;flex-direction:column;gap:6px;align-items:center;height:auto">
        <i class="fa-solid fa-house" style="font-size:22px;color:#64748b"></i>
        <span style="color:#0f172a">Anasayfaya Git</span>
        <small style="color:#94a3b8">/</small>
    </a>
</div>

<div class="alert alert-info">
    <i class="fa-solid fa-lightbulb"></i>
    <div>
        <strong>Sonraki adımlar:</strong>
        <ul style="margin:6px 0 0 18px;font-size:13px">
            <li>Mail driver'ı <strong>smtp</strong>'ye al — <em>Admin > Ayarlar > Mail</em></li>
            <li>SEO meta + GA4 tag — <em>Admin > Ayarlar > SEO</em></li>
            <li>Logo + renk paleti — sağ üst <strong>Düzenle</strong> butonu → Tema sidebar</li>
            <li>Tam belge: <a href="/docs/installation" style="color:#1d4ed8">/docs</a></li>
        </ul>
    </div>
</div>

<style>
    @keyframes bounce {
        0%   { transform: scale(.3); opacity: 0; }
        50%  { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection
