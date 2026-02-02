<?php

return [
    'driver' => 'sqlite', // Options: mysql, sqlite
    'host' => 'localhost',
    'database' => 'edm-mobile',
    'username' => 'root',
    'password' => '***REMOVED***',
    'path' => STORAGE_PATH . '/database.sqlite', // For SQLite
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'port' => 3306,
];
