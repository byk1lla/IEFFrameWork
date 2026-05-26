<?php
/**
 * Install wizard layout — diğer layoutlardan bağımsız.
 * Beklenen değişkenler: $_step (1-6), $title (opsiyonel), $body (HTML), $error (opsiyonel)
 */
$_step  = $_step  ?? 1;
$title  = $title  ?? 'Kurulum';
$steps  = [
    1 => ['Sistem',       'fa-server'],
    2 => ['Veritabanı',   'fa-database'],
    3 => ['Migrasyon',    'fa-arrow-down-up-across-line'],
    4 => ['Yönetici',     'fa-user-shield'],
    5 => ['Site Bilgisi', 'fa-globe'],
    6 => ['Tamam',        'fa-check'],
];
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= htmlspecialchars($title) ?> · IEF Kurulum</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 50%, #ecfeff 100%);
            min-height: 100vh;
            color: #0f172a;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 40px 20px;
        }
        .wrap {
            width: 100%; max-width: 720px;
        }

        /* Üst marka */
        .brand {
            text-align: center; margin-bottom: 30px;
        }
        .brand-mark {
            display: inline-flex; align-items: center; gap: 10px;
            font-weight: 800; font-size: 20px; color: #0f172a;
        }
        .brand-mark .logo {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb, #06b6d4);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px; box-shadow: 0 8px 20px -6px rgba(37,99,235,.4);
        }
        .brand small { display: block; color: #64748b; font-size: 13px; margin-top: 4px; }

        /* Stepper */
        .stepper {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 32px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
            padding: 16px; gap: 4px;
            box-shadow: 0 1px 3px rgba(15,23,42,.04);
        }
        .step { flex: 1; text-align: center; position: relative; }
        .step .num {
            width: 32px; height: 32px; border-radius: 50%;
            background: #f1f5f9; color: #94a3b8;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; margin-bottom: 6px;
            border: 2px solid #e2e8f0;
            transition: all .2s;
        }
        .step .label { font-size: 11px; color: #94a3b8; font-weight: 600; }
        .step.active .num {
            background: linear-gradient(135deg, #2563eb, #06b6d4); color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 16px -4px rgba(37,99,235,.45);
        }
        .step.active .label { color: #1d4ed8; }
        .step.done .num {
            background: #d1fae5; color: #065f46; border-color: #a7f3d0;
        }
        .step.done .label { color: #065f46; }
        .step:not(:last-child)::after {
            content: ''; position: absolute;
            top: 16px; right: -50%; width: 100%; height: 2px;
            background: #e2e8f0; z-index: -1;
        }
        /* Card */
        .card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 20px; padding: 32px;
            box-shadow: 0 4px 24px -8px rgba(15,23,42,.08);
        }
        .card-head { margin-bottom: 24px; }
        .card-head h1 {
            font-size: 24px; font-weight: 800; letter-spacing: -.02em; color: #0f172a;
            margin-bottom: 6px;
        }
        .card-head p { font-size: 14px; color: #64748b; line-height: 1.6; }

        /* Form */
        .field { margin-bottom: 18px; }
        .field label {
            display: block; font: 600 12.5px/1.4 'Inter';
            color: #334155; margin-bottom: 6px;
        }
        .field .hint { font-size: 11.5px; color: #94a3b8; margin-top: 4px; }
        .field input, .field select, .field textarea {
            width: 100%; padding: 11px 14px;
            border: 1px solid #cbd5e1; border-radius: 10px;
            font: 14px 'Inter'; color: #0f172a;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,.1);
        }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* Buttons */
        .actions {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px; border-radius: 10px;
            font: 600 14px 'Inter'; cursor: pointer;
            text-decoration: none; border: 0;
            transition: all .15s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #06b6d4); color: #fff;
            box-shadow: 0 6px 18px -4px rgba(37,99,235,.4);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 26px -4px rgba(37,99,235,.55); }
        .btn-ghost {
            background: #fff; color: #475569; border: 1px solid #e2e8f0;
        }
        .btn-ghost:hover { background: #f8fafc; border-color: #cbd5e1; }

        /* Alert */
        .alert {
            padding: 14px 16px; border-radius: 10px; margin-bottom: 18px;
            font-size: 13.5px; line-height: 1.55;
            display: flex; align-items: flex-start; gap: 12px;
        }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-warn  { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-info  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; }
        .alert-ok    { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert i { font-size: 16px; margin-top: 1px; flex-shrink: 0; }

        /* Check list (Step 1) */
        .checklist { list-style: none; }
        .checklist li {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 14px; border-radius: 10px; margin-bottom: 6px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            font-size: 13.5px;
        }
        .checklist li.ok { background: #f0fdf4; border-color: #bbf7d0; }
        .checklist li.fail { background: #fef2f2; border-color: #fecaca; }
        .checklist .left { display: flex; align-items: center; gap: 10px; }
        .checklist .icon {
            width: 24px; height: 24px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px;
        }
        .checklist .icon.ok { background: #16a34a; color: #fff; }
        .checklist .icon.fail { background: #dc2626; color: #fff; }
        .checklist .value { font: 500 12px 'JetBrains Mono', monospace; color: #64748b; }
        .checklist li.ok .value { color: #15803d; }
        .checklist li.fail .value { color: #991b1b; }

        /* Pre (migrate output) */
        pre.terminal {
            background: #0f172a; color: #e2e8f0;
            border-radius: 12px; padding: 16px;
            font: 12.5px/1.7 'JetBrains Mono', monospace;
            overflow-x: auto; margin: 12px 0;
            max-height: 320px;
        }
        pre.terminal .ok { color: #4ade80; }
        pre.terminal .fail { color: #f87171; }

        /* Footer */
        footer {
            text-align: center; margin-top: 24px;
            font-size: 12px; color: #94a3b8;
        }
        footer a { color: #2563eb; text-decoration: none; }

        @media (max-width: 600px) {
            .step .label { display: none; }
            .row { grid-template-columns: 1fr; }
            .card { padding: 24px 20px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand">
            <div class="brand-mark">
                <span class="logo">i</span>
                <span>ief<span style="color:#94a3b8;font-weight:500">-framework</span></span>
            </div>
            <small>Kurulum Sihirbazı · v<?= htmlspecialchars(\App\Core\Config::get('app.version', '2.0.0')) ?></small>
        </div>

        <div class="stepper">
            <?php foreach ($steps as $n => [$lbl, $ico]): ?>
                <div class="step <?= $n === $_step ? 'active' : ($n < $_step ? 'done' : '') ?>">
                    <div class="num">
                        <?php if ($n < $_step): ?>
                            <i class="fa-solid fa-check"></i>
                        <?php else: ?>
                            <?= $n ?>
                        <?php endif; ?>
                    </div>
                    <div class="label"><?= htmlspecialchars($lbl) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            @yield('content')
        </div>

        <footer>
            <a href="/docs/installation">Yardım & Dokümantasyon</a> · IEF Software © <?= date('Y') ?>
        </footer>
    </div>
</body>
</html>
