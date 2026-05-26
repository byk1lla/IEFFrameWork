<?php
/**
 * Create page_views table — in-house trafik loglama.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000060_create_page_views_table
{
    public function up(Database $db): void
    {
        Schema::create('page_views', function (Blueprint $t) {
            $t->id();
            $t->string('path', 500);
            $t->string('method', 8)->default('GET');
            $t->integer('status')->default(200);
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 500)->nullable();
            $t->string('referer', 500)->nullable();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->integer('response_time_ms')->nullable();
            $t->string('country', 4)->nullable();
            $t->date('day');
            $t->timestamp('created_at')->default('CURRENT_TIMESTAMP');
            $t->compositeIndex(['day']);
            $t->compositeIndex(['path', 'day']);
            $t->compositeIndex(['ip', 'created_at']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('page_views');
    }
}
