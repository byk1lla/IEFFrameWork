<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\MailService;
use App\Core\Logger;

class ContactController extends Controller
{
    public function index()
    {
        return $this->view('contact.index', [
            'title' => 'İletişim | IEF Framework'
        ]);
    }

    public function submit(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $message = $request->input('message');

        if (!$name || !$email || !$message) {
            $this->flash('error', 'Lütfen tüm alanları doldurun.');
            return $this->back();
        }

        // Simulate sending email
        Logger::info("İletişim Formu Gönderildi", [
            'from' => $name,
            'email' => $email,
            'msg' => $message
        ]);

        $this->flash('success', 'Mesajınız başarıyla günlüğe kaydedildi ve simüle edildi!');
        return $this->back();
    }
}
