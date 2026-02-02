<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class AuthController extends Controller
{
    // Local admin credentials (only this one)
    private const LOCAL_ADMIN = [
        'username' => 'byk1lla.local',
        'password' => 'Rootuser8112005.',
        'name' => 'Sistem Admin',
        'role' => 'admin'
    ];

    /**
     * Login formu göster
     */
    public function loginForm()
    {
        if (Session::isLoggedIn()) {
            return $this->redirect('/');
        }

        return $this->view('auth/login', [
            'title' => 'Giriş - E-Fatura Pro'
        ]);
    }

    /**
     * Login işlemi
     * - Local admin: byk1lla.local ile giriş
     * - EDM users: EDM Bilişim credentials ile giriş
     */
    public function login()
    {
        $username = trim($this->request->input('username') ?? '');
        $password = $this->request->input('password') ?? '';

        // 1. Local admin check
        if ($username === self::LOCAL_ADMIN['username']) {
            if ($password === self::LOCAL_ADMIN['password']) {
                Session::set('user', [
                    'id' => 'local_admin',
                    'username' => $username,
                    'name' => self::LOCAL_ADMIN['name'],
                    'role' => self::LOCAL_ADMIN['role'],
                    'vkn' => '56359306232', // Dursun Erdoğdu VKN
                    'is_local' => true,
                    'is_admin' => true
                ]);

                // Admin için EDM credentials'ı da ayarla (varsayılan)
                Session::set('edm_credentials', [
                    'username' => 'dursunerdogdu',
                    'password' => $password, // Admin kendi şifresini kullanacak
                    'wsdl' => 'https://portal.edmbilisim.com.tr/EFaturaEDM/EFaturaEDM.svc?singleWsdl'
                ]);

                $this->flash('success', 'Hoş geldiniz, Admin!');
                return $this->redirect('/');
            }

            $this->flash('error', 'Lütfen bilgilerinizi kontrol edin.');
            return $this->redirect('/login');
        }

        // 2. EDM Bilişim user login - username = EDM portal kullanıcı adı
        // Try EDM login
        try {
            require_once __DIR__ . '/../../edm-sdk/autoload.php';

            $wsdl = 'https://portal.edmbilisim.com.tr/EFaturaEDM/EFaturaEDM.svc?singleWsdl';
            $client = new \EFatura\Client($wsdl);

            if ($client->login($username, $password)) {
                // EDM login başarılı
                Session::set('user', [
                    'id' => 'edm_' . $username,
                    'username' => $username,
                    'name' => $username,
                    'vkn' => is_numeric($username) ? $username : '', // Username VKN ise ata
                    'role' => 'user',
                    'is_local' => false,
                    'is_admin' => false
                ]);

                Session::set('edm_credentials', [
                    'username' => $username,
                    'password' => $password,
                    'wsdl' => $wsdl
                ]);

                $client->logout();

                $this->flash('success', 'Hoş geldiniz, ' . $username);
                return $this->redirect('/');
            } else {
                $this->flash('error', 'Lütfen bilgilerinizi kontrol edin.');
                return $this->redirect('/login');
            }
        } catch (\Exception $e) {
            $this->flash('error', 'Lütfen bilgilerinizi kontrol edin.');
            return $this->redirect('/login');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        Session::destroy();
        return $this->redirect('/login');
    }
}
