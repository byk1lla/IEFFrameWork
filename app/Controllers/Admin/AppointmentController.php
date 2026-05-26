<?php
/**
 * Admin\AppointmentController — admin randevu yönetimi.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $filter = (string) $this->request->query('filter', 'all');
        $where  = match ($filter) {
            'pending'   => "WHERE status='pending'",
            'confirmed' => "WHERE status='confirmed'",
            'today'     => "WHERE DATE(start_at) = CURDATE()",
            'upcoming'  => "WHERE start_at >= NOW() AND status IN ('pending','confirmed')",
            'past'      => "WHERE start_at < NOW()",
            default     => '',
        };

        $rows = Database::getInstance()->fetchAll(
            "SELECT a.*, s.name AS service_name, s.duration_min
             FROM appointments a
             LEFT JOIN appointment_services s ON s.id = a.service_id
             $where
             ORDER BY a.start_at DESC LIMIT 300"
        );

        return $this->view('admin.appointments.index', [
            'rows'    => $rows,
            'filter'  => $filter,
            'summary' => Appointment::summary(),
        ]);
    }

    public function show(string $id): Response
    {
        $appt = Appointment::find($id);
        if (!$appt) return $this->notFound('Randevu bulunamadı.');
        return $this->view('admin.appointments.show', [
            'appt'    => $appt,
            'service' => $appt->service(),
        ]);
    }

    public function updateStatus(string $id): Response
    {
        $this->abortIfInvalidCsrf();
        $status = (string) $this->request->input('status', '');
        if (!in_array($status, Appointment::STATUSES, true)) {
            $this->flash('error', 'Geçersiz durum.');
            return $this->redirect("/admin/appointments/$id");
        }
        Appointment::updateById($id, [
            'status'      => $status,
            'admin_notes' => (string) $this->request->input('admin_notes', '') ?: null,
        ]);
        $this->flash('success', 'Durum güncellendi.');
        return $this->redirect("/admin/appointments/$id");
    }

    public function destroy(string $id): Response
    {
        $this->abortIfInvalidCsrf();
        Appointment::deleteById($id);
        $this->flash('success', 'Randevu silindi.');
        return $this->redirect('/admin/appointments');
    }
}
