<?php
/**
 * ALTER posts → SEO + featured alanları (AI ile Oluştur akışı için).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Database;

class m20260524_000120_alter_posts_add_seo_featured
{
    public function up(Database $db): void
    {
        if ($db->getDriver() === 'sqlite') {
            $cols = ['is_featured INTEGER DEFAULT 0', 'seo_title TEXT', 'seo_description TEXT', 'seo_keywords TEXT'];
            foreach ($cols as $c) {
                try { $db->exec("ALTER TABLE posts ADD COLUMN $c"); } catch (\Throwable) {}
            }
            return;
        }

        // MySQL — alan zaten varsa hatasız atla
        $existing = $db->fetchAll("SHOW COLUMNS FROM posts");
        $have     = array_column($existing, 'Field');
        $add      = [];
        if (!in_array('is_featured', $have, true))     $add[] = "ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0";
        if (!in_array('seo_title', $have, true))       $add[] = "ADD COLUMN `seo_title` VARCHAR(191) NULL";
        if (!in_array('seo_description', $have, true)) $add[] = "ADD COLUMN `seo_description` VARCHAR(300) NULL";
        if (!in_array('seo_keywords', $have, true))    $add[] = "ADD COLUMN `seo_keywords` VARCHAR(300) NULL";
        if ($add) {
            $db->exec("ALTER TABLE posts " . implode(', ', $add));
        }
    }

    public function down(Database $db): void
    {
        if ($db->getDriver() === 'sqlite') return; // SQLite drop column zor; no-op
        foreach (['is_featured','seo_title','seo_description','seo_keywords'] as $c) {
            try { $db->exec("ALTER TABLE posts DROP COLUMN `$c`"); } catch (\Throwable) {}
        }
    }
}
