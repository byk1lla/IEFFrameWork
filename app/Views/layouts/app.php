<!DOCTYPE html>
<html lang="{{ \App\Core\Lang::getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'IEF V4 | Titanium Obsidian' }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #050505;
            --obsidian: #0a0a0a;
            --purple: #8B5CF6;
            --cyan: #06B6D4;
            --text: #f8fafc;
            --text-dim: #94a3b8;
            --border: rgba(139, 92, 246, 0.15);
            --glow: 0 0 20px rgba(139, 92, 246, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(139, 92, 246, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(6, 182, 212, 0.05) 0%, transparent 40%);
        }

        .titan-nav {
            height: 90px;
            border-bottom: 1px solid var(--border);
            background: rgba(5, 5, 5, 0.8);
            backdrop-filter: blur(20px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-inner {
            max-width: 1400px;
            margin: 0 auto;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 60px;
        }

        .titan-logo {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: 4px;
            text-decoration: none;
            color: #fff;
            text-transform: uppercase;
        }

        .titan-logo span {
            color: var(--purple);
            text-shadow: var(--glow);
        }

        .nav-links {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dim);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            color: var(--purple);
            text-shadow: var(--glow);
        }

        .btn-v4 {
            background: linear-gradient(135deg, var(--purple), #7c3aed);
            color: #fff;
            padding: 12px 28px;
            border-radius: 4px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            font-size: 0.8rem;
            box-shadow: var(--glow);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-v4:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.4);
        }

        .app-stage {
            padding-top: 150px;
            min-height: 80vh;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 60px;
            padding-right: 60px;
        }

        footer {
            padding: 80px 0;
            text-align: center;
            border-top: 1px solid var(--border);
            color: var(--text-dim);
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Lang Switcher */
        .lang-titan {
            display: flex;
            gap: 15px;
            margin-right: 20px;
        }

        .lang-titan a {
            font-size: 0.75rem;
            font-weight: 900;
            color: var(--text-dim);
            text-decoration: none;
        }

        .lang-titan a.active {
            color: var(--cyan);
            text-shadow: 0 0 10px var(--cyan);
        }
    </style>
</head>

<body>
    <nav class="titan-nav">
        <div class="nav-inner">
            <a href="/" class="titan-logo">Titan<span>V4</span></a>
            <div class="nav-links">
                <div class="lang-titan">
                    <a href="/lang/tr" class="{{ \App\Core\Lang::getLocale() === 'tr' ? 'active' : '' }}">TR</a>
                    <a href="/lang/en" class="{{ \App\Core\Lang::getLocale() === 'en' ? 'active' : '' }}">EN</a>
                </div>
                <a href="/docs">{{ trans('knowledge') }}</a>
                <a href="/examples">{{ trans('ecosystem') }}</a>
                <a href="/admin" class="btn-v4">{{ trans('control_center') }}</a>
            </div>
        </div>
    </nav>

    <main class="app-stage">
        @yield('content')
    </main>

    <footer>
        IEF Framework V5.1 • Titanium Obsidian • 2026
    </footer>
</body>

</html>