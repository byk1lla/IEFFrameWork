<?php
/**
 * Create post_categories table — blog kategorileri.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000030_create_post_categories_table
{
    public function up(Database $db): void
    {
        Schema::create('post_categories', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('slug', 140)->unique();
            $t->string('description', 500)->nullable();
            $t->timestamps();
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('post_categories');
    }
}
