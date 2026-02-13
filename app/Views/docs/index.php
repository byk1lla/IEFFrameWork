<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEF Docs | Documentation</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #020617;
            --accent: #00D1FF;
            --border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: #fff;
            line-height: 1.7;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 100px 20px;
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--accent);
        }

        .doc-section {
            margin-bottom: 4rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 24px;
            border: 1px solid var(--border);
        }

        code {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }

        pre {
            background: #000;
            padding: 1.5rem;
            border-radius: 16px;
            overflow-x: auto;
            margin: 1rem 0;
            border: 1px solid var(--border);
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="/" style="color:var(--accent); text-decoration:none; display:block; margin-bottom:2rem;">&larr; Geri
            Dön</a>
        <h1>Dokümantasyon</h1>

        <div class="doc-section">
            <h2>Hızlı Başlangıç</h2>
            <p>IEF Framework ile saniyeler içinde API veya Web uygulaması geliştirmeye başlayın.</p>
            <pre><code>./ief make:controller Home</code></pre>
        </div>

        <div class="doc-section">
            <h2>Template Engine</h2>
            <p>Yeni Blade-lite engine ile temiz görünümler oluşturun.</p>
            <pre><code>@{{ $degisken }}<br>@foreach($veriler as $v)<br>  ...<br>@endforeach</code></pre>
        </div>

        <div class="doc-section">
            <h2>Bileşen Kütüphanesi</h2>
            <p>Hazır bileşenleri projenize dahil edin:</p>
            <pre><code>@include('components.button', ['label' => 'Gönder'])</code></pre>
        </div>
    </div>
</body>

</html>