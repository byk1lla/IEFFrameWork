<?php
/**
 * Eski basit page_views tablosunu kaldır (Onur trafik sistemine geçildi).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Database;
use App\Core\Schema;

class m20260524_000070_drop_old_page_views
{
    public function up(Database $db): void
    {
        Schema::dropIfExists('page_views');
    }

    public function down(Database $db): void
    {
        // No-op: eski tablo geri istenmiyor.
    }
}
