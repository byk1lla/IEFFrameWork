<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'IEF Framework | Next Gen PHP' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #020617;
            --primary: #00D1FF;
            --primary-dark: #003061;
            --secondary: #94a3b8;
            --glass: rgba(15, 23, 42, 0.7);
            --border: rgba(255, 255, 255, 0.08);
            --text-glow: rgba(0, 209, 255, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: #fff;
            line-height: 1.6;
            overflow-x: hidden;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(0, 48, 97, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(0, 209, 255, 0.05) 0%, transparent 50%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6rem;
            animation: slideDown 0.8s ease-out;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-box img {
            width: 48px;
            height: 32px;
            filter: brightness(0) invert(1);
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(to right, #fff, #9ca3af);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            margin-left: 2rem;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .hero {
            text-align: center;
            margin-bottom: 8rem;
            animation: fadeIn 1s ease-out;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent);
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.2);
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 5.5rem);
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1.5rem;
            letter-spacing: -3px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #fff 30%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .cta-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(0, 209, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 209, 255, 0.5);
            filter: brightness(1.1);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Bento Grid */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, 300px);
            gap: 1.5rem;
        }

        .bento-item {
            background: var(--card-bg);
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(12px);
        }

        .bento-item:hover {
            transform: scale(1.02);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .bento-1 {
            grid-column: span 2;
        }

        .bento-2 {
            grid-column: span 2;
        }

        .bento-3 {
            grid-column: span 1;
        }

        .bento-4 {
            grid-column: span 2;
        }

        .bento-5 {
            grid-column: span 1;
        }

        .item-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .item-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .item-content p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .code-snippet {
            margin-top: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: #818cf8;
            border-left: 3px solid var(--accent);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .bento-grid {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: auto;
            }

            .bento-item {
                grid-column: span 2 !important;
            }
        }

        footer {
            margin-top: 10rem;
            text-align: center;
            padding-bottom: 4rem;
            border-top: 1px solid var(--border);
            padding-top: 4rem;
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <nav>
            <div class="logo-box">
                <img src="/img/logo.png" alt="IEF Logo">
                <span class="logo-text">IEF FRAMEWORK</span>
            </div>
            <div class="nav-links">
                <a href="/docs">Dokümantasyon</a>
                <a href="/examples">Örnekler</a>
                <a href="/admin">Admin Paneli</a>
                <a href="https://github.com" target="_blank" class="btn btn-secondary"
                    style="padding: 0.6rem 1.2rem; font-size: 0.9rem;">GitHub</a>
            </div>
        </nav>

        <section class="hero">
            <div class="hero-badge">v{{ $version ?? '1.5.0' }} Yayınlandı</div>
            <h1>Yazılımda <span class="gradient-text">Yeni Standart</span></h1>
            <p>Hız, sadelik ve premium tasarımı birleştiren, modern PHP geliştirme ekosistemi.</p>
            <div class="cta-group">
                <a href="/examples" class="btn btn-primary">Örnek Uygulamaları Keşfet</a>
                <a href="/docs" class="btn btn-secondary">Hemen Başla</a>
            </div>
        </section>

        <section class="bento-grid">
            <div class="bento-item bento-1">
                <div class="item-icon">💎</div>
                <div class="item-content">
                    <h3>Premium Admin Dashboard</h3>
                    <p>Hazır gelen, Bento tasarım diline sahip yönetim paneli ile projelerinize bir adım önde başlayın.
                    </p>
                    <div class="code-snippet">Router::get('/admin', 'Admin@index');</div>
                </div>
            </div>

            <div class="bento-item bento-2">
                <div class="item-icon">🧩</div>
                <div class="item-content">
                    <h3>UI Component Library</h3>
                    <p>Reusable Button, Card ve Alert bileşenleri ile tutarlı ve hızlı arayüzler geliştirin.</p>
                    <div class="code-snippet">@include('components.button', ['type' => 'primary'])</div>
                </div>
            </div>

            <div class="bento-item bento-3">
                <div class="item-icon">🛠️</div>
                <div class="item-content">
                    <h3>Debug Bar</h3>
                    <p>Milisaniyelik performans takibi ve SQL profiler.</p>
                </div>
            </div>

            <div class="bento-item bento-4">
                <div class="item-icon">🔌</div>
                <div class="item-content">
                    <h3>Blade-lite Engine</h3>
                    <p>Gelişmiş direktifler ve layout desteği ile temiz görünümler.</p>
                    <div class="code-snippet">@extends('layouts.admin')</div>
                </div>
            </div>

            <div class="bento-item bento-5">
                <div class="item-icon">🛡️</div>
                <div class="item-content">
                    <h3>Zırhlı Güvenlik</h3>
                    <p>Advanced Exception UI ve CSRF koruması.</p>
                </div>
            </div>
        </section>

        <footer>
            <div class="footer-text">
                &copy; {{ date('Y') }} IEF Software. MIT License.
                <br><br>
                Crafted with love for modern developers.
            </div>
        </footer>
    </div>
</body>

</html>