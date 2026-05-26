<?php
/**
 * AuthController — login, logout, şifre sıfırlama akışı.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\MailService;

class AuthController extends Controller
{
    // ─── Login ─────────────────────────────────────────────────────
    public function showLogin(): Response
    {
        return $this->view('auth.login');
    }

    public function login(): Response
    {
        $this->abortIfInvalidCsrf();

        $email    = strtolower(trim((string) $this->request->input('email', '')));
        $password = (string) $this->request->input('password', '');

        if ($email === '' || $password === '') {
            $this->flash('error', 'E-posta ve şifre zorunludur.');
            return $this->redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            $this->flash('error', 'E-posta veya şifre hatalı.');
            return $this->redirect('/login');
        }

        $intended = Session::getFlash('intended_url') ?? '/admin';
        return $this->redirect((string) $intended);
    }

    public function logout(): Response
    {
        Auth::logout();
        return $this->redirect('/');
    }

    // ─── Şifre sıfırlama: TALEP ────────────────────────────────────
    public function showForgot(): Response
    {
        return $this->view('auth.forgot');
    }

    public function sendReset(): Response
    {
        $this->abortIfInvalidCsrf();

        $v = Validator::make($this->request->all(), [
            'email' => 'required|email|max:191',
        ]);

        if ($v->fails()) {
            $this->flash('error', 'Geçerli bir e-posta gir.');
            return $this->redirect('/sifre-sifirla');
        }

        $email = strtolower(trim((string) $this->request->input('email')));
        $user  = User::where('email', $email)->first();

        // Güvenlik: kullanıcı var mı yok mu söyleme, her durumda aynı mesajı dön.
        if ($user) {
            $token = PasswordReset::generateFor($email, $this->request->ip());
            $url   = rtrim((string) config('app.url', ''), '/') . "/sifre-sifirla/$token";

            MailService::send(
                $email,
                'Şifre sıfırlama bağlantınız',
                "Merhaba {$user->name},\n\nŞifreni sıfırlamak için aşağıdaki bağlantıyı kullan:\n\n$url\n\nBu bağlantı 60 dakika geçerlidir. Sen istemediysen bu mesajı görmezden gel.\n\n— " . config('app.name'),
                ['reply_to' => null]
            );
        }

        $this->flash('success', 'Eğer bu e-posta sistemde kayıtlıysa, sıfırlama bağlantısı gönderildi.');
        return $this->redirect('/login');
    }

    // ─── Şifre sıfırlama: TOKEN ile YENİ ŞİFRE ─────────────────────
    public function showReset(string $token): Response
    {
        $reset = PasswordReset::findValid($token);
        if (!$reset) {
            $this->flash('error', 'Bu sıfırlama bağlantısı geçersiz veya süresi dolmuş.');
            return $this->redirect('/sifre-sifirla');
        }
        return $this->view('auth.reset', ['token' => $token, 'email' => $reset['email']]);
    }

    public function performReset(string $token): Response
    {
        $this->abortIfInvalidCsrf();

        $reset = PasswordReset::findValid($token);
        if (!$reset) {
            $this->flash('error', 'Bu sıfırlama bağlantısı geçersiz veya süresi dolmuş.');
            return $this->redirect('/sifre-sifirla');
        }

        $v = Validator::make($this->request->all(), [
            'password' => 'required|min:6|max:191|confirmed',
        ]);

        if ($v->fails()) {
            $this->flash('error', 'Şifre en az 6 karakter olmalı ve onay alanıyla eşleşmeli.');
            return $this->redirect("/sifre-sifirla/$token");
        }

        $user = User::where('email', $reset['email'])->first();
        if (!$user) {
            $this->flash('error', 'Hesap bulunamadı.');
            return $this->redirect('/login');
        }

        User::updateById($user->id, [
            'password' => password_hash((string) $this->request->input('password'), PASSWORD_DEFAULT),
        ]);
        PasswordReset::markUsed((int) $reset['id']);

        $this->flash('success', 'Şifren güncellendi. Şimdi yeni şifrenle giriş yapabilirsin.');
        return $this->redirect('/login');
    }
}
