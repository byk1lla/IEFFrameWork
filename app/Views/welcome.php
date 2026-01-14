<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'IEF Framework' ?></title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --bg: #f3f4f6;
            --text: #1f2937;
            --muted: #6b7280;
            --card-bg: #ffffff;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            line-height: 1.5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .hero {
            text-align: center;
            padding: 5rem 0;
            animation: fadeIn 0.8s ease-out;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.05em;
        }
        .hero p {
            font-size: 1.5rem;
            color: var(--muted);
            margin-top: 1rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .version-badge {
            display: inline-block;
            background: #e0e7ff;
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1.5rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }
        .card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
        }
        .card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }
        .card p {
            color: var(--muted);
            margin: 0;
        }
        .card a {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .card a:hover {
            text-decoration: underline;
        }
        .footer {
            text-align: center;
            margin-top: 5rem;
            color: var(--muted);
            font-size: 0.875rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 2rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .code-block {
            background: #111827;
            color: #10b981;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: monospace;
            margin-top: 1rem;
            text-align: left;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <div class="version-badge">v<?= $version ?? '1.0.2' ?></div>
            <h1>IEF Framework</h1>
            <p>Sadeliğin ve Performansın Kusursuz Buluşması.</p>
            
            <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 1rem;">
                <a href="https://github.com/iefsoftware/ief-framework" target="_blank" style="background: #111827; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">GitHub</a>
                <a href="/tasks" style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">Örnek Uygulama</a>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <span class="card-icon">⚡</span>
                <h3>Yüksek Performans</h3>
                <p>Gereksiz katmanlardan arındırılmış, doğrudan sonuca odaklanan mimari ile ışık hızında yanıt süreleri.</p>
            </div>
            
            <div class="card">
                <span class="card-icon">🛣️</span>
                <h3>Güçlü Routing</h3>
                <p>RESTful rotalar, middleware desteği ve gruplama ile URL yapınızı kolayca yönetin.</p>
                <div class="code-block">Router::get('/users', 'UserController@index');</div>
            </div>

            <div class="card">
                <span class="card-icon">🔌</span>
                <h3>CLI Tool</h3>
                <p>Geliştirme sürecinizi hızlandıran konsol komutları.</p>
                <div class="code-block">./ief make:controller User<br>./ief make:model User</div>
            </div>

            <div class="card">
                <span class="card-icon">💾</span>
                <h3>Active Record</h3>
                <p>Modern ve güvenli veritabanı işlemleri. UUID desteği ve otomatik timestamp yönetimi.</p>
                <a href="#">Dokümantasyonu İncele &rarr;</a>
            </div>

            <div class="card">
                <span class="card-icon">🛡️</span>
                <h3>Güvenlik Odaklı</h3>
                <p>CSRF koruması, SQL Injection önlemleri ve XSS filtrelemesi varsayılan olarak gelir.</p>
            </div>

            <div class="card">
                <span class="card-icon">🎨</span>
                <h3>Modern Yapı</h3>
                <p>PHP 8.2+ özelliklerini kullanan, tip güvenli ve temiz kod yapısı.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> IEF Software. MIT License altında dağıtılmaktadır.</p>
            <p>
                <a href="#" style="color: var(--muted); margin: 0 0.5rem;">Dokümantasyon</a> • 
                <a href="#" style="color: var(--muted); margin: 0 0.5rem;">GitHub</a> • 
                <a href="#" style="color: var(--muted); margin: 0 0.5rem;">Destek</a>
            </p>
        </div>
    </div>
</body>
</html>
