<?php
/**
 * Create media table — galeri ve dosya kütüphanesi.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000020_create_media_table
{
    public function up(Database $db): void
    {
        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('disk', 32)->default('public');
            $t->string('bucket', 64)->default('media');
            $t->string('path', 500);          // /uploads/<bucket>/<filename>
            $t->string('original_name', 255);
            $t->string('mime', 100)->nullable();
            $t->unsignedBigInteger('size')->default(0);
            $t->string('alt', 255)->nullable();
            $t->unsignedBigInteger('uploaded_by')->nullable();
            $t->timestamps();
            $t->index();   // bucket
            $t->compositeIndex(['bucket', 'created_at']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('media');
    }
}
