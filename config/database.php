<?php

return [
    'driver' => 'sqlite', // Options: mysql, sqlite
    'host' => 'localhost',
    'database' => 'ief_db',
    'username' => 'root',
    'password' => '',
    'path' => STORAGE_PATH . '/database.sqlite', // For SQLite
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'port' => 3306,
];
