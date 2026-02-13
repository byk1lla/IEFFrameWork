<?php

return [
    'driver' => 'mysql', // Options: mysql, sqlite
    'host' => 'localhost',
    'database' => 'your_database',
    'username' => 'your_username',
    'password' => 'your_password',
    'path' => STORAGE_PATH . '/database.sqlite', // For SQLite
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'port' => 3306,
];
