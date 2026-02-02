<!DOCTYPE html>
<html lang="tr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - E-Fatura Pro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Custom ring spinner */
        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-loading {
            pointer-events: none;
            opacity: 0.8;
        }
    </style>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl mb-4">
                <i class="fas fa-file-invoice-dollar text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">E-Fatura Pro</h1>
            <p class="text-gray-400 mt-2">EDM Bilişim E-Fatura Yönetimi</p>
        </div>

        <!-- Login Form -->
        <form id="loginForm" action="/login" method="POST" class="glass rounded-2xl p-8 space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">

            <!-- Flash Messages -->
            <?php if ($error = \App\Core\Session::getFlash('error')): ?>
                <div
                    class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success = \App\Core\Session::getFlash('success')): ?>
                <div
                    class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <div class="text-center mb-4">
                <p class="text-gray-400 text-sm">
                    EDM Bilişim hesabınızla giriş yapın
                </p>
            </div>

            <!-- Username -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Kullanıcı Adı</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" id="username" required autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="EDM kullanıcı adınız">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Şifre</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 rounded-xl hover:shadow-lg hover:shadow-blue-500/30 transition flex items-center justify-center gap-2">
                <span id="btnText">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </span>
                <span id="btnLoader" class="hidden">
                    <div class="spinner"></div>
                </span>
            </button>

            <p class="text-center text-gray-500 text-xs mt-4">
                EDM Bilişim portal şifrenizi kullanın.<br>
                Sistem yöneticileri için özel hesap: <code class="text-blue-400">*.local</code>
            </p>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            E-Fatura Pro v2.0 &copy; 2026
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Disable button and show spinner
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
        });
    </script>

</body>

</html>