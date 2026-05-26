<?php
/**
 * Appointment — randevu kaydı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Appointment extends Model
{
    protected static string $table = 'appointments';

    protected static array $fillable = [
        'service_id', 'customer_name', 'email', 'phone',
        'start_at', 'end_at', 'status', 'notes', 'admin_notes', 'ip',
    ];

    public const STATUSES = ['pending', 'confirmed', 'done', 'cancelled'];

    public function service(): ?AppointmentService
    {
        if (!isset($this->attributes['service_id'])) return null;
        return AppointmentService::find($this->attributes['service_id']);
    }

    public static function summary(): array
    {
        $db = Database::getInstance();
        return [
            'pending'   => (int) ($db->fetch("SELECT COUNT(*) c FROM appointments WHERE status='pending'")['c'] ?? 0),
            'confirmed' => (int) ($db->fetch("SELECT COUNT(*) c FROM appointments WHERE status='confirmed'")['c'] ?? 0),
            'today'     => (int) ($db->fetch("SELECT COUNT(*) c FROM appointments WHERE DATE(start_at)=CURDATE()")['c'] ?? 0),
            'upcoming'  => (int) ($db->fetch("SELECT COUNT(*) c FROM appointments WHERE start_at >= NOW() AND status IN ('pending','confirmed')")['c'] ?? 0),
        ];
    }

    /** Belirli tarihte (00:00–23:59) randevu var mı kontrol et — başka bir slot ile çakışıyorsa true. */
    public static function isSlotTaken(string $startAt, string $endAt): bool
    {
        $row = Database::getInstance()->fetch(
            "SELECT 1 FROM appointments
             WHERE status IN ('pending','confirmed')
             AND (
                (start_at < ? AND end_at > ?)
                OR (start_at < ? AND end_at > ?)
                OR (start_at >= ? AND end_at <= ?)
             ) LIMIT 1",
            [$endAt, $startAt, $startAt, $startAt, $startAt, $endAt]
        );
        return $row !== null;
    }
}
