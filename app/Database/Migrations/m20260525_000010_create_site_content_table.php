<?php
/**
 * Create site_content table — Onur tarzı canlı (inline) editör için KV deposu.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260525_000010_create_site_content_table
{
    public function up(Database $db): void
    {
        Schema::create('site_content', function (Blueprint $t) {
            $t->id();
            $t->string('content_key', 191)->unique();
            $t->string('content_type', 16)->default('text'); // text|html|image|icon|url|number
            $t->longText('value')->nullable();
            $t->string('page', 80)->nullable();
            $t->string('section', 80)->nullable();
            $t->string('label', 200)->nullable();
            $t->timestamps();
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('site_content');
    }
}
