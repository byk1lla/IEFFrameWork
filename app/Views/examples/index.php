<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
          :root { --bg: #020617; --accent: #00D1FF; --border: rgba(255, 255, 255, 0.08); }
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: #fff;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            text-align: center;
        }

        .examples-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .example-card {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid var(--border);
            border-radius: 32px;
            padding: 3rem;
            transition: all 0.4s;
            text-decoration: none;
            color: inherit;
        }

        .example-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent);
            background: rgba(99, 102, 241, 0.05);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 2rem;
            display: block;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .card-desc {
            color: #9ca3af;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="/"
            style="color:var(--accent); text-decoration:none; display:inline-block; margin-bottom:3rem; font-weight:600;">←
            Ana Sayfaya Dön</a>
        <h1>Örnek <span style="color:var(--accent)">Uygulamalar</span></h1>

        <div class="examples-grid">
            @foreach($examples as $ex)
            <a href="{{ $ex['url'] }}" class="example-card">
                <span class="card-icon">{{ $ex['icon'] }}</span>
                <div class="card-title">{{ $ex['name'] }}</div>
                <div class="card-desc">{{ $ex['desc'] }}</div>
            </a>
            @endforeach
        </div>
    </div>
</body>

</html>