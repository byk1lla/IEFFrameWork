<?php
// Setup Database for CRUD Example
date_default_timezone_set('Europe/Istanbul');

// Define Paths
define('ROOT_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Load Composer Autoload
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

echo "Setting up SQLite database...\n";

// Ensure storage directory exists
if (!file_exists(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0777, true);
    echo "Created storage directory.\n";
}

try {
    // Database class will automatically use Config class which will check CONFIG_PATH
    $db = Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS tasks (
        id VARCHAR(36) PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        is_completed TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $db->execute($sql);
    echo "Table 'tasks' created successfully.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
