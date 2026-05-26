<?php
/**
 * Create error_reports table — exception sayfasından gönderilen hatalar.
 *
 * Aynı hata (hash bazlı) tek satırda agrega edilir; count + last_seen_at güncellenir.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260526_120000_create_error_reports_table
{
    public function up(Database $db): void
    {
        Schema::create('error_reports', function (Blueprint $t) {
            $t->id();
            $t->string('hash', 64)->unique();          // sha256(exception+file+line)
            $t->string('exception', 191);
            $t->text('message')->nullable();
            $t->string('file', 500)->nullable();
            $t->integer('line')->nullable();
            $t->longText('trace')->nullable();         // JSON
            $t->longText('request_data')->nullable(); // JSON: url, method, body, headers
            $t->longText('server_data')->nullable();   // JSON: PHP, OS, framework
            $t->string('user_note', 500)->nullable(); // "ne yapıyordun?"
            $t->string('reporter_ip', 64)->nullable();
            $t->integer('occurrence_count')->default(1);
            $t->integer('mail_sent_count')->default(0);
            $t->boolean('is_fixed')->default(0);
            $t->timestamp('first_seen_at')->nullable();
            $t->timestamp('last_seen_at')->nullable();
            $t->timestamp('fixed_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('error_reports');
    }
}
