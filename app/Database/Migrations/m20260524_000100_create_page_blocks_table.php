<?php
/**
 * Create page_blocks table — inline CMS / site editör blokları.
 *
 * Her blok: <page>.<key>  (örn. home.hero.title)
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000100_create_page_blocks_table
{
    public function up(Database $db): void
    {
        Schema::create('page_blocks', function (Blueprint $t) {
            $t->id();
            $t->string('page', 64);              // home, blog, iletisim ...
            $t->string('block_key', 128);        // hero.title, hero.subtitle ...
            $t->longText('content')->nullable();
            $t->string('type', 16)->default('text'); // text | html | image
            $t->string('label', 191)->nullable();
            $t->timestamps();
            $t->uniqueIndex(['page', 'block_key']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('page_blocks');
    }
}
