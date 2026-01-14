<?php
/**
 * Ana Uygulama Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class App
{
    protected static ?App $instance = null;
    protected array $config = [];
    protected ?Database $db = null;

    public function __construct()
    {
        self::$instance = $this;
        $this->loadConfig();
    }

    public static function getInstance(): ?App
    {
        return self::$instance;
    }

    protected function loadConfig(): void
    {
        $this->config['app'] = require CONFIG_PATH . '/app.php';
        Config::load();
    }

    public function run(): void
    {
        // Bakım modu kontrolü
        if ($this->config['app']['maintenance']['enabled'] ?? false) {
            $allowedIps = $this->config['app']['maintenance']['allowed_ips'] ?? [];
            if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
                http_response_code(503);
                echo $this->config['app']['maintenance']['message'];
                exit;
            }
        }

        // Session başlat
        Session::start();

        // Router çalıştır
        require CONFIG_PATH . '/routes.php';
        Router::dispatch();
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config['app'][$key] ?? $default;
    }

    public function getDatabase(): Database
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }
}
