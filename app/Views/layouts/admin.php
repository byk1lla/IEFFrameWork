<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #020617;
            --sidebar: #0f172a;
            --card: rgba(15, 23, 42, 0.7);
            --accent: #00D1FF;
            --accent-dark: #003061;
            --border: rgba(255, 255, 255, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: #fff;
            overflow: hidden;
        }

        .admin-wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            color: var(--accent);
        }

        .nav-menu {
            flex: 1;
        }

        .nav-item {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            color: #fff;
            transition: all 0.3s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(0, 209, 255, 0.1);
            color: #fff;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 3rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="logo">IEF ADMIN</div>
            <nav class="nav-menu">
                <div class="nav-item active">🏠 Dashboard</div>
                <div class="nav-item">👥 Kullanıcılar</div>
                <div class="nav-item">📑 Raporlar</div>
                <div class="nav-item">⚙️ Ayarlar</div>
            </nav>
        </aside>
        <main class="main-content">
            <header class="header">
                <h2>Hoş Geldiniz, {{ $authUser['name'] ?? 'Geliştirici' }}</h2>
                <div class="user-profile">
                    <div class="avatar"></div>
                    <span>Yönetici</span>
                </div>
            </header>
            @yield('content')
        </main>
    </div>
</body>

</html>