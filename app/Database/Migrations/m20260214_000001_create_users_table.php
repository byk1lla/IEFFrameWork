<?php

namespace App\Database\Migrations;

use App\Core\Database;

class m20260214_000001_create_users_table
{
    public function up(Database $db)
    {
        $db->execute("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            avatar VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function down(Database $db)
    {
        $db->execute("DROP TABLE IF EXISTS users");
    }
}
