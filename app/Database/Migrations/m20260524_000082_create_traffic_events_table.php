<?php
/**
 * Create traffic_events table — frontend etkileşim olayları (click/scroll/form vs.).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000082_create_traffic_events_table
{
    public function up(Database $db): void
    {
        Schema::create('traffic_events', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('session_id');
            $t->string('event_type', 32);              // page_view | click | scroll | form_submit | video_play ...
            $t->string('path', 500)->nullable();
            $t->string('target', 300)->nullable();
            $t->string('value', 500)->nullable();
            $t->timestamp('occurred_at')->default('CURRENT_TIMESTAMP');
            $t->compositeIndex(['session_id']);
            $t->compositeIndex(['event_type']);
            $t->compositeIndex(['occurred_at']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('traffic_events');
    }
}
