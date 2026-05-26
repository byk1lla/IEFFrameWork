<?php
/**
 * InstallController — web tabanlı kurulum sihirbazı.
 *
 * Shared hosting (cPanel/Plesk) ve VPS için uygun. SSH gerektirmez.
 *
 * Akış:
 *  /install              → step 1: sistem kontrolü
 *  /install/database     → step 2: DB credentials + test
 *  /install/migrate      → step 3: tabloları kur
 *  /install/admin        → step 4: ilk admin kullanıcı
 *  /install/site         → step 5: site bilgileri
 *  /install/done         → step 6: bitir + lock yaz
 *
 * Güvenlik:
 *  - storage/installed.lock dosyası varsa hepsi 404 döner.
 *  - Sadece kurulum bitince /install'a girilemez.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;
use App\Core\MigrationRunner;
use App\Core\Session;

class InstallController extends Controller
{
    public const LOCK_PATH = ROOT_PATH . '/storage/installed.lock';

    /**
     * Lock varsa hiç bir adım çalışmasın — early abort.
     */
    protected function guardNotInstalled(): void
    {
        if (file_exists(self::LOCK_PATH)) {
            http_response_code(404);
            echo '<!doctype html><meta charset="utf-8"><title>404</title>'
               . '<div style="font:16px/1.5 system-ui;max-width:520px;margin:80px auto;text-align:center">'
               . '<h1 style="font-size:64px;margin:0;color:#0f172a">404</h1>'
               . '<p style="color:#64748b">Kurulum zaten tamamlanmış. <a href="/" style="color:#2563eb">Anasayfa</a></p>'
               . '</div>';
            exit;
        }
    }

    /**
     * Step 1 — Sistem & ortam kontrolü
     */
    public function index(): Response
    {
        $this->guardNotInstalled();

        $checks = $this->systemChecks();
        $allOk = !in_array(false, array_column($checks, 'ok'), true);

        return $this->render('install.step1-system', [
            'checks' => $checks,
            'allOk'  => $allOk,
        ]);
    }

    /**
     * Step 2 — DB credentials form + test
     */
    public function database(): Response
    {
        $this->guardNotInstalled();

        if ($this->request->isMethod('POST')) {
            $data = [
                'driver'   => $this->request->input('driver', 'mysql'),
                'host'     => trim($this->request->input('host', '127.0.0.1')),
                'port'     => (int) $this->request->input('port', 3306),
                'database' => trim($this->request->input('database', '')),
                'username' => trim($this->request->input('username', '')),
                'password' => $this->request->input('password', ''),
                'path'     => trim($this->request->input('path', ROOT_PATH . '/storage/db.sqlite')),
            ];

            // Test bağlantı
            $test = $this->testConnection($data);
            if (!$test['ok']) {
                Session::set('install.error', $test['error']);
                Session::set('install.db_form', $data);
                return $this->redirect('/install/database');
            }

            // config/database.php dosyasını yaz
            $this->writeDatabaseConfig($data);
            Session::set('install.db', $data);

            return $this->redirect('/install/migrate');
        }

        return $this->render('install.step2-database', [
            'form'  => Session::get('install.db_form', []),
            'error' => Session::get('install.error'),
        ]);
    }

    /**
     * Step 3 — Migrasyonlar
     */
    public function migrate(): Response
    {
        $this->guardNotInstalled();

        $result = null;
        if ($this->request->isMethod('POST')) {
            try {
                // Yeni config'i okut (database.php sihirbazda yeniden yazıldı)
                \App\Core\Config::load(true);
                Database::reset();

                // Output'u yakala ve UI'da göster
                ob_start();
                (new MigrationRunner())->migrate();
                $output = ob_get_clean();

                // ANSI renk kodlarını temizle
                $output = preg_replace('/\033\[[0-9;]*m/', '', (string) $output);

                $result = ['ok' => true, 'output' => trim($output)];
                Session::set('install.migrated', true);
                Session::set('install.migrate_output', $result['output']);
                return $this->redirect('/install/admin');
            } catch (\Throwable $e) {
                $result = ['ok' => false, 'error' => $e->getMessage()];
            }
        }

        return $this->render('install.step3-migrate', ['result' => $result]);
    }

    /**
     * Step 4 — İlk admin
     */
    public function admin(): Response
    {
        $this->guardNotInstalled();
        if (!Session::get('install.migrated')) return $this->redirect('/install/migrate');

        $error = null;
        if ($this->request->isMethod('POST')) {
            $name  = trim($this->request->input('name', ''));
            $email = trim($this->request->input('email', ''));
            $pass  = $this->request->input('password', '');
            $pass2 = $this->request->input('password_confirmation', '');

            if ($name === '' || $email === '' || $pass === '') {
                $error = 'İsim, e-posta ve şifre zorunlu.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Geçersiz e-posta.';
            } elseif (strlen($pass) < 8) {
                $error = 'Şifre en az 8 karakter olmalı.';
            } elseif ($pass !== $pass2) {
                $error = 'Şifreler eşleşmedi.';
            } else {
                try {
                    $db = Database::getInstance();
                    $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    if ($existing) {
                        $db->execute("UPDATE users SET name = ?, password = ?, role = ? WHERE id = ?", [$name, $hash, 'superadmin', $existing['id']]);
                    } else {
                        $db->execute("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)", [$name, $email, $hash, 'superadmin']);
                    }
                    Session::set('install.admin_created', true);
                    return $this->redirect('/install/site');
                } catch (\Throwable $e) {
                    $error = 'DB hatası: ' . $e->getMessage();
                }
            }
        }

        return $this->render('install.step4-admin', ['error' => $error]);
    }

    /**
     * Step 5 — Site bilgileri (opsiyonel)
     */
    public function site(): Response
    {
        $this->guardNotInstalled();
        if (!Session::get('install.admin_created')) return $this->redirect('/install/admin');

        if ($this->request->isMethod('POST')) {
            $fields = [
                'general.site_name'     => trim($this->request->input('site_name', '')),
                'general.site_tagline'  => trim($this->request->input('site_tagline', '')),
                'general.contact_email' => trim($this->request->input('contact_email', '')),
                'general.contact_phone' => trim($this->request->input('contact_phone', '')),
                'general.timezone'      => trim($this->request->input('timezone', 'Europe/Istanbul')),
                'general.locale'        => trim($this->request->input('locale', 'tr')),
            ];

            try {
                foreach ($fields as $key => $value) {
                    if ($value !== '') {
                        \App\Models\Setting::set($key, $value, 'string');
                    }
                }
            } catch (\Throwable $e) { /* settings tablosu yoksa atla */ }

            return $this->redirect('/install/done');
        }

        return $this->render('install.step5-site');
    }

    /**
     * Step 6 — Tamamlandı + lock yaz
     */
    public function done(): Response
    {
        $this->guardNotInstalled();
        if (!Session::get('install.admin_created')) return $this->redirect('/install/admin');

        // Lock dosyası — bir daha /install'a girilemez
        @file_put_contents(self::LOCK_PATH, json_encode([
            'installed_at' => date('c'),
            'version'      => \App\Core\Config::get('app.version', '2.0.0'),
        ], JSON_PRETTY_PRINT));

        // Session install:* anahtarlarını temizle
        Session::set('install.db', null);
        Session::set('install.db_form', null);
        Session::set('install.migrated', null);
        Session::set('install.admin_created', null);
        Session::set('install.error', null);

        return $this->render('install.step6-done');
    }

    /* ───────────────────────── Yardımcılar ───────────────────────── */

    /**
     * Render — install/* view'ları admin/app layout'una bağımlı olmamalı.
     */
    protected function render(string $template, array $data = []): Response
    {
        $data['_step'] = $this->stepNumber($template);
        return $this->view($template, $data);
    }

    protected function stepNumber(string $tpl): int
    {
        return match (true) {
            str_contains($tpl, 'step1') => 1,
            str_contains($tpl, 'step2') => 2,
            str_contains($tpl, 'step3') => 3,
            str_contains($tpl, 'step4') => 4,
            str_contains($tpl, 'step5') => 5,
            str_contains($tpl, 'step6') => 6,
            default => 1,
        };
    }

    /**
     * Sistem kontrolü — PHP, extension, klasör izinleri, vendor.
     */
    protected function systemChecks(): array
    {
        $checks = [];

        // PHP versiyon
        $checks[] = [
            'label' => 'PHP 8.1+',
            'ok'    => version_compare(PHP_VERSION, '8.1', '>='),
            'value' => PHP_VERSION,
        ];

        // Extension'lar
        foreach (['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'fileinfo'] as $ext) {
            $checks[] = [
                'label' => "ext-{$ext}",
                'ok'    => extension_loaded($ext),
                'value' => extension_loaded($ext) ? '✓' : 'eksik',
            ];
        }

        // Vendor
        $checks[] = [
            'label' => 'vendor/autoload.php',
            'ok'    => file_exists(ROOT_PATH . '/vendor/autoload.php'),
            'value' => file_exists(ROOT_PATH . '/vendor/autoload.php') ? '✓' : 'composer install çalıştır',
        ];

        // Klasör yazma izinleri
        foreach ([
            'storage'                       => STORAGE_PATH,
            'storage/logs'                  => STORAGE_PATH . '/logs',
            'storage/cache'                 => STORAGE_PATH . '/cache',
            'storage/sessions'              => STORAGE_PATH . '/sessions',
            'public/uploads'                => PUBLIC_PATH . '/uploads',
            'public/assets/img/content'     => PUBLIC_PATH . '/assets/img/content',
            'config (yazma)'                => CONFIG_PATH,
        ] as $label => $path) {
            if (!is_dir($path)) @mkdir($path, 0775, true);
            $checks[] = [
                'label' => $label,
                'ok'    => is_dir($path) && is_writable($path),
                'value' => is_dir($path) ? (is_writable($path) ? 'yazılabilir' : 'yazılamıyor — chmod 775') : 'yok',
            ];
        }

        return $checks;
    }

    /**
     * DB bağlantı testi.
     */
    protected function testConnection(array $data): array
    {
        try {
            if ($data['driver'] === 'sqlite') {
                $path = $data['path'];
                $dir  = dirname($path);
                if (!is_dir($dir)) @mkdir($dir, 0775, true);
                $pdo = new \PDO('sqlite:' . $path);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->query('SELECT 1');
                return ['ok' => true];
            }

            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $data['host'], $data['port'], $data['database']);
            $pdo = new \PDO($dsn, $data['username'], $data['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);
            $pdo->query('SELECT 1');
            return ['ok' => true];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * config/database.php dosyasını yaz.
     */
    protected function writeDatabaseConfig(array $data): void
    {
        if ($data['driver'] === 'sqlite') {
            $content = "<?php\nreturn [\n    'driver' => 'sqlite',\n    'path'   => " . var_export($data['path'], true) . ",\n];\n";
        } else {
            $content = "<?php\n"
                     . "/**\n * Veritabanı — kurulum sihirbazıyla yapılandırıldı.\n */\nreturn [\n"
                     . "    'driver'    => 'mysql',\n"
                     . "    'host'      => " . var_export($data['host'], true) . ",\n"
                     . "    'port'      => " . (int) $data['port'] . ",\n"
                     . "    'database'  => " . var_export($data['database'], true) . ",\n"
                     . "    'username'  => " . var_export($data['username'], true) . ",\n"
                     . "    'password'  => " . var_export($data['password'], true) . ",\n"
                     . "    'charset'   => 'utf8mb4',\n"
                     . "    'collation' => 'utf8mb4_unicode_ci',\n"
                     . "];\n";
        }
        file_put_contents(CONFIG_PATH . '/database.php', $content);
    }
}
