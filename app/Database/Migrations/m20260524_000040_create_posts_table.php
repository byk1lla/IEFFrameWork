<?php
/**
 * Create posts table — blog yazıları.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000040_create_posts_table
{
    public function up(Database $db): void
    {
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('category_id')->nullable();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('title', 200);
            $t->string('slug', 220)->unique();
            $t->string('excerpt', 500)->nullable();
            $t->longText('content');
            $t->string('cover_image', 500)->nullable();
            $t->boolean('published')->default(0);
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->compositeIndex(['published', 'published_at']);
            $t->compositeIndex(['category_id', 'published']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('posts');
    }
}
