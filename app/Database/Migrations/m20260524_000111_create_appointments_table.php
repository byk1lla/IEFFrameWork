<?php
/**
 * Create appointments table — randevu kayıtları.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000111_create_appointments_table
{
    public function up(Database $db): void
    {
        Schema::create('appointments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('service_id')->nullable();
            $t->string('customer_name', 120);
            $t->string('email', 191);
            $t->string('phone', 32)->nullable();
            $t->dateTime('start_at');
            $t->dateTime('end_at');
            $t->string('status', 16)->default('pending'); // pending | confirmed | done | cancelled
            $t->text('notes')->nullable();
            $t->text('admin_notes')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
            $t->compositeIndex(['start_at']);
            $t->compositeIndex(['status', 'start_at']);
            $t->compositeIndex(['service_id']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('appointments');
    }
}
