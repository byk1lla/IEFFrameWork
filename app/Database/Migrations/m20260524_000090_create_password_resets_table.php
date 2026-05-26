<?php
/**
 * Create password_resets table — şifre sıfırlama token'ları.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000090_create_password_resets_table
{
    public function up(Database $db): void
    {
        Schema::create('password_resets', function (Blueprint $t) {
            $t->id();
            $t->string('email', 191);
            $t->string('token', 100)->unique();
            $t->timestamp('expires_at');
            $t->timestamp('used_at')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamp('created_at')->default('CURRENT_TIMESTAMP');
            $t->compositeIndex(['email']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('password_resets');
    }
}
