<?php
/**
 * Create settings table — KV store (anahtar/değer + grup + tip).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Core\Blueprint;
use App\Core\Database;
use App\Core\Schema;

class m20260524_000050_create_settings_table
{
    public function up(Database $db): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('group', 64)->default('general');
            $t->string('key', 128);
            $t->text('value')->nullable();
            $t->string('type', 24)->default('string'); // string | int | bool | json
            $t->timestamps();
            $t->uniqueIndex(['group', 'key']);
        });
    }

    public function down(Database $db): void
    {
        Schema::dropIfExists('settings');
    }
}
