<?php
/**
 * Mail Konfigürasyonu — şimdilik stub
 *
 * @package IEF Framework
 */

return [
    'driver' => 'log', // log | smtp | sendmail (gelecekte)

    'from' => [
        'address' => 'no-reply@iefsoftware.tr',
        'name'    => 'IEF Framework',
    ],

    'smtp' => [
        'host'       => 'smtp.example.com',
        'port'       => 587,
        'username'   => '',
        'password'   => '',
        'encryption' => 'tls', // tls | ssl | null
    ],
];
