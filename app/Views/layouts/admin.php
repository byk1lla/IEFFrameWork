<!DOCTYPE html>
<html lang="{{ \App\Core\Lang::getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titan Admin | IEF V4</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #050505;
            --obsidian: #080808;
            --purple: #8B5CF6;
            --cyan: #06B6D4;
            --text: #f8fafc;
            --text-dim: #64748b;
            --border: rgba(139, 92, 246, 0.1);
            --glow: 0 0 20px rgba(139, 92, 246, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            overflow: hidden;
        }

        .titan-layout {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Obsidian */
        .sidebar {
            width: 280px;
            background: var(--obsidian);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .sidebar::after {
            content: "";
            position: absolute;
            right: -1px;
            top: 10%;
            height: 80%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, var(--purple), transparent);
        }

        .sidebar-header {
            padding: 50px 40px;
        }

        .titan-logo {
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: 3px;
            color: #fff;
            text-transform: uppercase;
        }

        .titan-logo span {
            color: var(--purple);
            text-shadow: var(--glow);
        }

        .nav-list {
            flex: 1;
            padding: 0 20px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px 25px;
            border-radius: 4px;
            color: var(--text-dim);
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.75rem;
            transition: all 0.3s;
            margin-bottom: 8px;
            border: 1px solid transparent;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(139, 92, 246, 0.05);
            border-color: var(--border);
        }

        .nav-link.active {
            color: var(--purple);
            background: rgba(139, 92, 246, 0.08);
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: var(--glow);
        }

        .sidebar-footer {
            padding: 40px;
            border-top: 1px solid var(--border);
        }

        /* Main Stage */
        .main-stage {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .stage-header {
            height: 100px;
            padding: 0 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            background: rgba(5, 5, 5, 0.8);
            backdrop-filter: blur(20px);
        }

        .stage-title h2 {
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .lang-switch {
            display: flex;
            gap: 12px;
        }

        .lang-switch a {
            font-size: 0.7rem;
            font-weight: 900;
            color: var(--text-dim);
            text-decoration: none;
            transition: all 0.3s;
        }

        .lang-switch a.active {
            color: var(--cyan);
            text-shadow: 0 0 10px var(--cyan);
        }

        .u-meta {
            text-align: right;
        }

        .u-name {
            display: block;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .u-role {
            font-size: 0.65rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .u-avatar {
            width: 45px;
            height: 45px;
            border-radius: 4px;
            background: linear-gradient(135deg, var(--purple), #4c1d95);
            box-shadow: var(--glow);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stage-view {
            flex: 1;
            overflow-y: auto;
            padding: 60px;
        }

        /* V4 Components */
        .titan-card {
            background: var(--obsidian);
            border: 1px solid var(--border);
            padding: 40px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .titan-card:hover {
            border-color: var(--purple);
            box-shadow: var(--glow);
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
        }
    </style>
</head>

<body>
    <div class="titan-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="titan-logo">Titan<span>V4</span></div>
            </div>
            <nav class="nav-list">
                <a href="/admin" class="nav-link active">⚡ {{ trans('control_center') }}</a>
                <a href="/blog" class="nav-link">✍️ {{ trans('experience') }}</a>
                <a href="/contact" class="nav-link">✉️ {{ trans('ecosystem') }}</a>
                <a href="/docs" class="nav-link">📜 {{ trans('knowledge') }}</a>
                <a href="/" class="nav-link">🌌 {{ trans('nexus_gateway') }}</a>
            </nav>
            <div class="sidebar-footer">
                <div style="font-size: 0.6rem; color: var(--text-dim); letter-spacing: 2px; text-transform: uppercase;">
                    {{ trans('system_status') }}
                </div>
            </div>
        </aside>

        <main class="main-stage">
            <header class="stage-header">
                <div class="stage-title">
                    <h2>{{ $title ?? trans('control_center') }}</h2>
                </div>
                <div class="user-panel">
                    <div class="lang-switch">
                        <a href="/lang/tr" class="{{ \App\Core\Lang::getLocale() === 'tr' ? 'active' : '' }}">TR</a>
                        <a href="/lang/en" class="{{ \App\Core\Lang::getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    </div>
                    <div class="u-meta">
                        <span class="u-name">{{ $authUser['name'] ?? 'Core Architect' }}</span>
                        <span class="u-role">{{ trans('administrator') }}</span>
                    </div>
                    <div class="u-avatar"></div>
                </div>
            </header>
            <div class="stage-view">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>