<?php
/**
 * 500 Internal Server Error sayfası — production (debug=false) için kullanılır.
 * ExceptionHandler bu view'ı render eder.
 *
 * Beklenen değişkenler:
 *   $siteName, $contactEmail, $errorId (opsiyonel — log'a düşmüş hatanın referansı)
 */
$siteName     = $siteName     ?? 'Site';
$contactEmail = $contactEmail ?? '';
$errorId      = $errorId      ?? null;
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Bir şeyler ters gitti · <?= htmlspecialchars($siteName, ENT_QUOTES) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --brand:#2563eb; --accent:#06b6d4;
            --bg:#fff; --bg-alt:#f8fafc;
            --text:#0f172a; --text-dim:#64748b;
            --border:#e2e8f0;
        }
        body{
            font-family:'Inter',system-ui,sans-serif;
            background:var(--bg); color:var(--text);
            min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            padding:24px; position:relative; overflow-x:hidden;
        }

        /* Animasyonlu arka plan */
        body::before,body::after{
            content:''; position:fixed; border-radius:50%;
            filter:blur(90px); opacity:.32; pointer-events:none; z-index:0;
        }
        body::before{
            top:-180px; left:-180px; width:520px; height:520px;
            background:radial-gradient(circle,#fecaca 0%,transparent 70%);
            animation:f1 22s ease-in-out infinite;
        }
        body::after{
            bottom:-220px; right:-200px; width:620px; height:620px;
            background:radial-gradient(circle,#a5f3fc 0%,transparent 70%);
            animation:f2 26s ease-in-out infinite;
        }
        @keyframes f1{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(80px,80px) scale(1.18)}}
        @keyframes f2{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-110px,-60px) scale(1.12)}}

        .grid-bg{
            position:fixed; inset:0; pointer-events:none; z-index:0;
            background-image:
                linear-gradient(rgba(15,23,42,.04) 1px,transparent 1px),
                linear-gradient(90deg,rgba(15,23,42,.04) 1px,transparent 1px);
            background-size:50px 50px;
            mask-image:radial-gradient(circle at center,black 0%,transparent 80%);
            -webkit-mask-image:radial-gradient(circle at center,black 0%,transparent 80%);
        }

        .wrap{
            position:relative; z-index:1;
            max-width:680px; width:100%;
            text-align:center;
            display:flex; flex-direction:column; align-items:center;
        }

        .badge{
            display:inline-flex; align-items:center; gap:8px;
            padding:8px 16px;
            background:rgba(220,38,38,.08);
            border:1px solid rgba(220,38,38,.22);
            border-radius:999px;
            font:600 12px Inter; color:#b91c1c;
            margin-bottom:28px;
            animation:fdown .6s ease both;
        }
        .badge .dot{
            width:8px; height:8px; border-radius:50%;
            background:#dc2626;
            box-shadow:0 0 0 0 rgba(220,38,38,.6);
            animation:pulse 2s infinite;
        }

        .icon-box{
            width:96px; height:96px;
            background:linear-gradient(135deg,#dc2626,#f59e0b);
            border-radius:24px;
            margin-bottom:28px;
            box-shadow:0 20px 50px -10px rgba(220,38,38,.4),0 0 0 1px rgba(255,255,255,.8) inset;
            display:inline-flex; align-items:center; justify-content:center;
            animation:fup .7s .1s ease both;
        }
        .icon-box i{
            font-size:44px; color:#fff;
            filter:drop-shadow(0 4px 8px rgba(0,0,0,.15));
            animation:shake 4s 1s ease-in-out infinite;
        }
        @keyframes shake{
            0%,100%{transform:rotate(0)}
            5%{transform:rotate(-8deg)}
            10%{transform:rotate(8deg)}
            15%{transform:rotate(-4deg)}
            20%{transform:rotate(0)}
        }

        h1{
            font:800 clamp(38px,6vw,56px) Inter;
            letter-spacing:-.025em; line-height:1.05;
            margin-bottom:16px;
            animation:fup .7s .2s ease both;
        }
        h1 .gradient{
            background:linear-gradient(135deg,#dc2626 0%,#f59e0b 100%);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
        }

        .lead{
            font:17px/1.7 Inter;
            color:var(--text-dim);
            max-width:520px;
            margin:0 auto 32px;
            animation:fup .7s .3s ease both;
        }
        .lead strong{color:var(--text); font-weight:600}

        .error-id{
            display:inline-flex; align-items:center; gap:10px;
            padding:11px 18px;
            background:var(--bg-alt);
            border:1px solid var(--border);
            border-radius:12px;
            font:13px 'JetBrains Mono',monospace;
            color:var(--text-dim);
            margin-bottom:28px;
            animation:fup .7s .35s ease both;
        }
        .error-id .label{font:600 10.5px Inter;text-transform:uppercase;letter-spacing:.1em;color:var(--text-dim)}
        .error-id .val{color:var(--brand); font-weight:600}

        .actions{
            display:inline-flex; flex-wrap:wrap; gap:12px; justify-content:center;
            margin-top:8px;
            animation:fup .7s .5s ease both;
        }
        .btn{
            display:inline-flex; align-items:center; gap:8px;
            padding:13px 26px;
            border-radius:12px;
            font:600 14px Inter; cursor:pointer; text-decoration:none;
            border:0; transition:all .2s;
        }
        .btn-primary{
            background:linear-gradient(135deg,var(--brand),var(--accent));
            color:#fff;
            box-shadow:0 10px 30px -8px rgba(37,99,235,.55);
        }
        .btn-primary:hover{transform:translateY(-2px); box-shadow:0 14px 40px -8px rgba(37,99,235,.7)}
        .btn-ghost{
            background:#fff; color:var(--text);
            border:1px solid var(--border);
        }
        .btn-ghost:hover{
            background:var(--bg-alt); border-color:#cbd5e1;
        }

        .contact{
            margin-top:36px; padding-top:28px;
            border-top:1px solid var(--border);
            font:14px Inter; color:var(--text-dim);
            animation:fade 1s .7s ease both;
        }
        .contact a{color:var(--brand); text-decoration:none; font-weight:600}
        .contact a:hover{text-decoration:underline}

        footer{
            position:relative;
            margin-top:24px;
            font:12px Inter; color:var(--text-dim);
            animation:fade 1s .8s ease both;
        }
        footer strong{color:var(--text); font-weight:600}

        @keyframes fdown{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fup{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fade{from{opacity:0}to{opacity:1}}
        @keyframes pulse{
            0%{box-shadow:0 0 0 0 rgba(220,38,38,.55)}
            70%{box-shadow:0 0 0 10px rgba(220,38,38,0)}
            100%{box-shadow:0 0 0 0 rgba(220,38,38,0)}
        }
    </style>
</head>
<body>
    <div class="grid-bg"></div>

    <div class="wrap">
        <span class="badge">
            <span class="dot"></span>
            HTTP 500 · Sunucu Hatası
        </span>

        <div class="icon-box">
            <i class="fa-solid fa-bug"></i>
        </div>

        <h1>Bir <span class="gradient">şeyler ters</span> gitti</h1>

        <p class="lead">
            Bu sayfayı yüklerken beklenmedik bir hata oluştu.
            <strong>Sorun kayıt altına alındı</strong> ve ekibimiz haberdar.
            Birkaç dakika içinde tekrar deneyebilirsin.
        </p>

        <?php if ($errorId): ?>
            <div class="error-id">
                <span class="label">Hata ID:</span>
                <span class="val"><?= htmlspecialchars($errorId, ENT_QUOTES) ?></span>
            </div>
        <?php endif; ?>

        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i> Yeniden Dene
            </button>
            <a href="/" class="btn btn-ghost">
                <i class="fa-solid fa-house"></i> Anasayfaya Dön
            </a>
            <a href="javascript:history.back()" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Geri Git
            </a>
        </div>

        <?php if ($contactEmail !== ''): ?>
            <div class="contact">
                Sorun devam ederse:
                <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES) ?><?= $errorId ? '?subject=Hata%20Raporu%20%23' . urlencode((string)$errorId) : '' ?>">
                    <?= htmlspecialchars($contactEmail, ENT_QUOTES) ?>
                </a>
            </div>
        <?php endif; ?>

        <footer>
            © <?= date('Y') ?> <strong><?= htmlspecialchars($siteName, ENT_QUOTES) ?></strong>
        </footer>
    </div>
</body>
</html>
