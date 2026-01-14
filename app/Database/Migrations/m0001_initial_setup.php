<?php

namespace App\Database\Migrations;

class m0001_initial_setup
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id VARCHAR(36) PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            is_completed TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->execute($sql);
    }

    public function down($db)
    {
        $sql = "DROP TABLE IF EXISTS tasks";
        $db->execute($sql);
    }
}
