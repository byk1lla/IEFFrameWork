<?php
/**
 * AppointmentService — randevu sırasında seçilebilecek servis tipleri.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class AppointmentService extends Model
{
    protected static string $table = 'appointment_services';
    protected static array  $fillable = ['name', 'slug', 'description', 'duration_min', 'price', 'active'];

    public static function active(): array
    {
        return static::query()->whereCondition('active', 1)->orderBy('id', 'ASC')->get();
    }
}
