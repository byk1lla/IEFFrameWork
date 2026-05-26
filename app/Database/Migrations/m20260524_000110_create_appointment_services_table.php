<?php
/**
 * Create appointment_services table + örnek 3 servis seed.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000110_create_appointment_services_table
{
    public function up(Database $db): void
    {
        Schema::create('appointment_services', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('slug', 140)->unique();
            $t->string('description', 500)->nullable();
            $t->integer('duration_min')->default(30);
            $t->decimal('price', 10, 2)->default(0);
            $t->boolean('active')->default(1);
            $t->timestamps();
        });

        // Örnek 3 servis
        $now = date('Y-m-d H:i:s');
        $samples = [
            ['Tanışma Görüşmesi', 'tanisma',   'Kısa, ücretsiz tanışma toplantısı.', 30, 0],
            ['Standart Danışmanlık', 'standart','60 dakikalık birebir danışmanlık.',  60, 750],
            ['Detaylı Analiz',    'detayli',   '90 dakikalık derinlemesine analiz.', 90, 1200],
        ];
        foreach ($samples as $s) {
            $db->execute(
                "INSERT INTO appointment_services (name, slug, description, duration_min, price, active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, ?, ?)",
                [$s[0], $s[1], $s[2], $s[3], $s[4], $now, $now]
            );
        }
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('appointment_services');
    }
}
