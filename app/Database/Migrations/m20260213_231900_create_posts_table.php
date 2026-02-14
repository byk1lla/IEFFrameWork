<?php

namespace App\Database\Migrations;

use App\Core\Database;

class m20260213_231900_create_posts_table
{
    public function up(Database $db)
    {
        $db->execute("CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            author VARCHAR(100) DEFAULT 'Admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function down(Database $db)
    {
        $db->execute("DROP TABLE IF EXISTS posts");
    }
}
