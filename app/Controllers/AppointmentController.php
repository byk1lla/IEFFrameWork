<?php
/**
 * AppointmentController — public randevu sayfası.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Services\MailService;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        return $this->view('appointments.index', [
            'services' => AppointmentService::active(),
            'minDate'  => date('Y-m-d', strtotime('+1 day')),
            'maxDate'  => date('Y-m-d', strtotime('+60 days')),
        ]);
    }

    public function book(): Response
    {
        $this->abortIfInvalidCsrf();

        $v = Validator::make($this->request->all(), [
            'service_id'    => 'required|integer',
            'customer_name' => 'required|min:2|max:120',
            'email'         => 'required|email|max:191',
            'phone'         => 'max:32',
            'date'          => 'required|date',
            'time'          => 'required',
            'notes'         => 'max:1000',
        ]);

        if ($v->fails()) {
            $errs = $v->errors();
            $first = reset($errs);
            $this->flash('error', is_array($first) ? ($first[0] ?? 'Form hatası') : (string) $first);
            return $this->redirect('/randevu');
        }

        $service = AppointmentService::find((int) $this->request->input('service_id'));
        if (!$service || !(int) $service->active) {
            $this->flash('error', 'Seçilen servis bulunamadı.');
            return $this->redirect('/randevu');
        }

        $start = trim((string) $this->request->input('date')) . ' ' . trim((string) $this->request->input('time')) . ':00';
        $startTs = strtotime($start);
        if (!$startTs || $startTs < time()) {
            $this->flash('error', 'Geçmiş bir tarih/saat seçemezsin.');
            return $this->redirect('/randevu');
        }
        $endTs   = $startTs + ((int) $service->duration_min * 60);
        $startAt = date('Y-m-d H:i:s', $startTs);
        $endAt   = date('Y-m-d H:i:s', $endTs);

        if (Appointment::isSlotTaken($startAt, $endAt)) {
            $this->flash('error', 'Seçtiğin saat dolu görünüyor. Başka bir saat dene.');
            return $this->redirect('/randevu');
        }

        $appt = Appointment::create([
            'service_id'    => (int) $service->id,
            'customer_name' => trim((string) $this->request->input('customer_name')),
            'email'         => strtolower(trim((string) $this->request->input('email'))),
            'phone'         => trim((string) $this->request->input('phone', '')) ?: null,
            'start_at'      => $startAt,
            'end_at'        => $endAt,
            'status'        => 'pending',
            'notes'         => trim((string) $this->request->input('notes', '')) ?: null,
            'ip'            => $this->request->ip(),
        ]);

        // Müşteriye bilgi maili
        MailService::send(
            (string) $appt->email,
            'Randevu talebin alındı',
            "Merhaba {$appt->customer_name},\n\nRandevu talebin alınmıştır:\n\n"
            . "Servis: {$service->name}\nTarih:  " . format_datetime($startAt) . "\nSüre:   {$service->duration_min} dk\n\n"
            . "Talebi onayladığımızda sana ayrıca dönüş yapacağız.\n\n— " . config('app.name')
        );

        // Yöneticiye bildirim
        $admin = (string) config('app.contact_to', config('mail.from.address'));
        MailService::send(
            $admin,
            'Yeni randevu talebi: ' . $service->name,
            "Müşteri: {$appt->customer_name} <{$appt->email}>\nTelefon: " . ($appt->phone ?? '-') . "\n"
            . "Servis:  {$service->name}\nTarih:   " . format_datetime($startAt) . "\n\nNot: " . ($appt->notes ?? '-')
        );

        return $this->redirect('/randevu/tesekkurler?id=' . $appt->id);
    }

    public function thanks(): Response
    {
        $id   = (int) $this->request->query('id', 0);
        $appt = $id ? Appointment::find($id) : null;
        return $this->view('appointments.thanks', ['appt' => $appt]);
    }
}
