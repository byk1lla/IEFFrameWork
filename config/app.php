<?php
/**
 * Uygulama Konfigürasyonu
 *
 * Tüm uygulama düzeyindeki ayarlar burada. .env KULLANMIYORUZ —
 * production'a göre değerleri doğrudan bu dosyada değiştir.
 *
 * @package IEF Framework
 */

return [

    /*
     * Uygulama kimliği
     */
    'name'     => 'IEF Framework',
    'version'  => '2.0.0',
    'env'      => 'development', // development | production
    'debug'    => true,          // production'da false yap
    'url'      => 'http://localhost:8000',
    'timezone' => 'Europe/Istanbul',
    'locale'   => 'tr',          // tr | en
    'fallback_locale' => 'en',
    'key'      => 'ief-framework-secret-key-change-me', // session/crypt için

    /*
     * Bakım modu (true ise allowed_ips dışındakilere 503 döner)
     */
    'maintenance' => [
        'enabled'     => false,
        'allowed_ips' => ['127.0.0.1', '::1'],
        'message'     => 'Sistem bakımda. Lütfen daha sonra tekrar deneyin.',
    ],

    /*
     * Provider/Service kayıtları (gelecekte service container için)
     */
    'providers' => [
        // \App\Providers\RouteServiceProvider::class,
    ],

    /*
     * Role tanımları — can()/is_role() helper'ları bunu okur
     */
    'roles' => [
        'superadmin' => [
            'name'        => 'Süper Admin',
            'permissions' => ['*'],
        ],
        'admin' => [
            'name'        => 'Admin',
            'permissions' => ['admin.*'],
        ],
        'user' => [
            'name'        => 'Kullanıcı',
            'permissions' => ['profile.*'],
        ],
    ],
];
