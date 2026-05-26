<?php
/**
 * Create users table — kullanıcı/auth tablosu.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000001_create_users_table
{
    public function up(Database $db): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('email', 191)->unique();
            $t->string('password');
            $t->string('role', 32)->default('user');
            $t->string('avatar')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('users');
    }
}
