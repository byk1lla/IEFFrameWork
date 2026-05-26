<?php
/**
 * Create messages table — iletişim formu mesajları.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000010_create_messages_table
{
    public function up(Database $db): void
    {
        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('email', 191);
            $t->string('phone', 32)->nullable();
            $t->string('subject', 200)->nullable();
            $t->text('body');
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->boolean('is_read')->default(0);
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->compositeIndex(['is_read', 'created_at']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('messages');
    }
}
