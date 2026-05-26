<?php
/**
 * Bakım modu sayfası — plain PHP (View engine yüklemeden render edilir).
 *
 * Beklenen değişkenler (App::serveMaintenancePage'den):
 *   $siteName, $contactEmail, $contactPhone, $waNumber, $eta, $message
 */
$siteName     = $siteName     ?? 'Site';
$contactEmail = $contactEmail ?? '';
$contactPhone = $contactPhone ?? '';
$waNumber     = $waNumber     ?? '';
$eta          = $eta          ?? '';
$message      = $message      ?? '';
$waDigits     = $waNumber !== '' ? preg_replace('/[^0-9]/', '', $waNumber) : '';
$year         = date('Y');
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Bakım Modu · <?= htmlspecialchars($siteName, ENT_QUOTES) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --brand-50:  #eff6ff;
            --brand-100: #dbeafe;
            --brand-200: #bfdbfe;
            --brand-500: #3b82f6;
            --brand-600: #2563eb;
            --brand-700: #1d4ed8;
            --brand-900: #1e3a8a;
            --accent:    #06b6d4;
            --bg:        #ffffff;
            --bg-alt:    #f8fafc;
            --text:      #0f172a;
            --text-dim:  #64748b;
            --border:    #e2e8f0;
        }
        body {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }

        /* Soft background blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .35;
            pointer-events: none;
            z-index: 0;
        }
        body::before {
            top: -150px; left: -150px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, #bfdbfe 0%, transparent 70%);
            animation: float1 18s ease-in-out infinite;
        }
        body::after {
            bottom: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, #a5f3fc 0%, transparent 70%);
            animation: float2 22s ease-in-out infinite;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%      { transform: translate(80px, 80px) scale(1.2); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%      { transform: translate(-100px, -60px) scale(1.15); }
        }

        /* Grid pattern */
        .grid-bg {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(15, 23, 42, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, .04) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(circle at center, black 0%, transparent 80%);
            -webkit-mask-image: radial-gradient(circle at center, black 0%, transparent 80%);
            pointer-events: none;
            z-index: 0;
        }

        .wrap {
            position: relative; z-index: 1;
            max-width: 720px; width: 100%;
            text-align: center;
            display: flex; flex-direction: column; align-items: center;
        }

        /* Brand badge */
        .badge {
            display: inline-flex;
            align-items: center; gap: 8px;
            padding: 8px 16px;
            background: var(--brand-50);
            border: 1px solid var(--brand-200);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: var(--brand-700);
            margin-bottom: 28px;
            animation: fadeInDown .6s ease forwards;
        }
        .badge .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--brand-500);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, .6);
            animation: pulseSoft 2s infinite;
        }

        /* Icon box — kutu SABİT, içindeki çark döner */
        .icon-box {
            display: inline-flex;
            align-items: center; justify-content: center;
            width: 96px; height: 96px;
            background: linear-gradient(135deg, var(--brand-600), var(--accent));
            border-radius: 24px;
            margin-bottom: 28px;
            box-shadow:
                0 20px 50px -10px rgba(37, 99, 235, .35),
                0 0 0 1px rgba(255, 255, 255, .8) inset;
            animation: fadeInUp .7s .1s ease both;
        }
        .icon-box i {
            font-size: 44px;
            color: #fff;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, .15));
            animation: gearSpin 8s linear infinite;
        }
        @keyframes gearSpin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: clamp(38px, 6vw, 56px);
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.05;
            margin-bottom: 16px;
            color: var(--text);
            animation: fadeInUp .7s .2s ease both;
        }
        h1 .gradient {
            background: linear-gradient(135deg, var(--brand-600) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .lead {
            font-size: 17px;
            color: var(--text-dim);
            max-width: 520px;
            margin: 0 auto 32px;
            line-height: 1.7;
            animation: fadeInUp .7s .3s ease both;
        }
        .lead strong { color: var(--text); font-weight: 600; }

        /* ETA card */
        .eta {
            display: inline-flex;
            align-items: center; gap: 12px;
            padding: 14px 22px;
            background: var(--brand-50);
            border: 1px solid var(--brand-200);
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            color: var(--brand-700);
            margin-bottom: 32px;
            animation: fadeInUp .7s .35s ease both;
        }
        .eta i { font-size: 16px; }

        /* Action buttons */
        .actions {
            display: inline-flex; flex-wrap: wrap; gap: 12px; justify-content: center;
            margin-top: 24px;
            animation: fadeInUp .7s .5s ease both;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 26px;
            border-radius: 12px;
            font: 600 14px 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all .25s ease;
            border: 0;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--brand-600), var(--accent));
            color: #fff;
            box-shadow: 0 10px 30px -8px rgba(37, 99, 235, .45);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 40px -8px rgba(37, 99, 235, .6); }
        .btn-primary:active { transform: translateY(0); }
        .btn-ghost {
            background: #fff;
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover {
            background: var(--brand-50);
            border-color: var(--brand-200);
            color: var(--brand-700);
            transform: translateY(-2px);
        }

        /* Contact card */
        .contact {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 24px;
            margin: 32px auto 0;
            max-width: 520px;
            box-shadow: 0 4px 24px -8px rgba(15, 23, 42, .08);
            animation: fadeInUp .7s .4s ease both;
        }
        .contact h3 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-dim);
            margin-bottom: 18px;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
        }
        .contact a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 14px;
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all .2s ease;
        }
        .contact a:hover {
            background: var(--brand-50);
            border-color: var(--brand-200);
            transform: translateY(-2px);
            color: var(--brand-700);
        }
        .contact a i { font-size: 14px; }
        .contact a.wa:hover { color: #25d366; border-color: rgba(37, 211, 102, .35); background: rgba(37, 211, 102, .08); }

        /* Footer */
        footer {
            position: relative;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-dim);
            animation: fadeIn 1s .7s ease both;
        }
        footer strong { color: var(--text); font-weight: 600; }
        footer .status {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 8px;
            font-size: 11px;
            color: var(--text-dim);
        }
        footer .status code {
            background: var(--bg-alt);
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'SF Mono', Menlo, monospace;
            font-size: 10.5px;
            color: var(--brand-700);
            border: 1px solid var(--border);
        }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp   { from { opacity: 0; transform: translateY(16px); }  to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn     { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulseSoft  {
            0%   { box-shadow: 0 0 0 0 rgba(59, 130, 246, .55); }
            70%  { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        @media (max-width: 480px) {
            .icon-box { width: 80px; height: 80px; border-radius: 20px; }
            .icon-box i { font-size: 36px; }
            .lead { font-size: 15px; }
            .contact { padding: 22px 18px; }
        }
    </style>
</head>
<body>
    <div class="grid-bg"></div>

    <div class="wrap">
        <span class="badge">
            <span class="dot"></span>
            HTTP 503 · Geçici Kesinti
        </span>

        <div class="icon-box">
            <i class="fa-solid fa-gear"></i>
        </div>

        <h1>
            Sistem <span class="gradient">Bakımda</span>
        </h1>

        <p class="lead">
            <?php if ($message !== ''): ?>
                <?= htmlspecialchars($message, ENT_QUOTES) ?>
            <?php else: ?>
                <strong><?= htmlspecialchars($siteName, ENT_QUOTES) ?></strong> şu anda planlı bir bakımdan geçiyor.
                Çok kısa süre içinde geri dönüyoruz — anlayışınız için teşekkürler.
            <?php endif; ?>
        </p>

        <?php if ($eta !== ''): ?>
            <div class="eta">
                <i class="fa-regular fa-clock"></i>
                <span>Tahmini açılış: <strong style="color:var(--text);font-weight:600"><?= htmlspecialchars($eta, ENT_QUOTES) ?></strong></span>
            </div>
            <br>
        <?php endif; ?>

        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i> Yeniden Dene
            </button>
            <a href="/login" class="btn btn-ghost">
                <i class="fa-solid fa-right-to-bracket"></i> Giriş Yap
            </a>
        </div>

        <?php if ($contactEmail !== '' || $contactPhone !== '' || $waDigits !== ''): ?>
            <div class="contact">
                <h3>Acil durumlarda bize ulaşın</h3>
                <div class="contact-grid">
                    <?php if ($contactEmail !== ''): ?>
                        <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES) ?>">
                            <i class="fa-regular fa-envelope"></i> E-posta
                        </a>
                    <?php endif; ?>
                    <?php if ($contactPhone !== ''): ?>
                        <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $contactPhone), ENT_QUOTES) ?>">
                            <i class="fa-solid fa-phone"></i> Telefon
                        </a>
                    <?php endif; ?>
                    <?php if ($waDigits !== ''): ?>
                        <a class="wa" href="https://wa.me/<?= htmlspecialchars($waDigits, ENT_QUOTES) ?>" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <footer>
            © <?= $year ?> <strong><?= htmlspecialchars($siteName, ENT_QUOTES) ?></strong>
            <div class="status">
                <code>HTTP/1.1 503 Service Unavailable</code>
            </div>
        </footer>
    </div>

    <script>
        // Sayfa açıldıktan 60 saniye sonra otomatik yenile
        setTimeout(() => location.reload(), 60000);
    </script>
</body>
</html>
