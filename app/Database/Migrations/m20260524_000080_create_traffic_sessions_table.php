<?php
/**
 * Create traffic_sessions table — tekil ziyaretçi (fingerprint bazlı).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000080_create_traffic_sessions_table
{
    public function up(Database $db): void
    {
        Schema::create('traffic_sessions', function (Blueprint $t) {
            $t->id();
            $t->string('fingerprint', 64)->unique();   // sha256(ip + ua + salt)
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 500)->nullable();
            $t->string('device', 24)->nullable();      // desktop | mobile | tablet
            $t->string('browser', 32)->nullable();
            $t->string('os', 32)->nullable();
            $t->string('country', 4)->nullable();
            $t->boolean('is_bot')->default(0);
            $t->string('bot_reason', 50)->nullable();
            $t->string('referrer_host', 191)->nullable();
            $t->string('utm_source', 80)->nullable();
            $t->string('utm_medium', 80)->nullable();
            $t->string('utm_campaign', 120)->nullable();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->integer('request_count')->default(0);
            $t->timestamp('first_seen_at')->default('CURRENT_TIMESTAMP');
            $t->timestamp('last_seen_at')->default('CURRENT_TIMESTAMP');
            $t->compositeIndex(['is_bot']);
            $t->compositeIndex(['last_seen_at']);
            $t->compositeIndex(['ip']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('traffic_sessions');
    }
}
