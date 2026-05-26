<?php
/**
 * Create traffic_logs table — HTTP istek logu (her request bir satır).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000081_create_traffic_logs_table
{
    public function up(Database $db): void
    {
        Schema::create('traffic_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('session_id');
            $t->string('method', 10)->default('GET');
            $t->string('path', 500);
            $t->string('query_string', 500)->nullable();
            $t->integer('status_code')->default(200);
            $t->integer('response_ms')->default(0);
            $t->string('referrer', 500)->nullable();
            $t->boolean('is_bot')->default(0);
            $t->timestamp('occurred_at')->default('CURRENT_TIMESTAMP');
            $t->compositeIndex(['session_id']);
            $t->compositeIndex(['path']);
            $t->compositeIndex(['status_code']);
            $t->compositeIndex(['is_bot']);
            $t->compositeIndex(['occurred_at']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('traffic_logs');
    }
}
