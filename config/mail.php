<?php

return [
    'from_email' => 'noreply@iefsoftware.tr',
    'from_name' => 'IEF Framework',
    'driver' => 'mail', // Options: mail, smtp

    // SMTP Configuration (For future implementation)
    'smtp' => [
        'host' => 'localhost',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls'
    ]
];
