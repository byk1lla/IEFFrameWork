<?php
/**
 * ContactController — iletişim formu (public).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Message;
use App\Services\MailService;

class ContactController extends Controller
{
    public function index(): Response
    {
        return $this->view('contact.index');
    }

    public function submit(): Response
    {
        $this->abortIfInvalidCsrf();

        $v = Validator::make($this->request->all(), [
            'name'    => 'required|min:2|max:120',
            'email'   => 'required|email|max:191',
            'phone'   => 'max:32',
            'subject' => 'max:200',
            'body'    => 'required|min:5|max:5000',
        ]);

        if ($v->fails()) {
            Session::flash('contact_errors', $v->errors());
            Session::flash('_old_input', $this->request->only(['name','email','phone','subject','body']));
            return $this->redirect('/iletisim');
        }

        $message = Message::create([
            'name'       => trim((string) $this->request->input('name')),
            'email'      => trim((string) $this->request->input('email')),
            'phone'      => trim((string) $this->request->input('phone', '')) ?: null,
            'subject'    => trim((string) $this->request->input('subject', '')) ?: null,
            'body'       => trim((string) $this->request->input('body')),
            'ip'         => $this->request->ip(),
            'user_agent' => substr((string) $this->request->userAgent(), 0, 255),
            'is_read'    => 0,
        ]);

        // Yöneticiye bildirim maili (log driver default)
        $adminMail = (string) Config::get('app.contact_to', Config::get('mail.from.address', 'admin@example.com'));
        MailService::send(
            $adminMail,
            'Yeni iletişim mesajı: ' . ($message->subject ?? '(konusuz)'),
            "Gönderen: {$message->name} <{$message->email}>\nTelefon: " . ($message->phone ?? '-') . "\n\n{$message->body}",
            ['reply_to' => $message->email]
        );

        return $this->redirect('/iletisim/tesekkurler');
    }

    public function thanks(): Response
    {
        return $this->view('contact.thanks');
    }
}
