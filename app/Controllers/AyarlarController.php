<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class AyarlarController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            $this->redirect('/login');
            exit;
        }
    }

    public function index()
    {
        $edmCredentials = Session::get('edm_credentials');

        return $this->view('ayarlar/index', [
            'title' => 'Ayarlar - E-Fatura Pro',
            'edm_username' => $edmCredentials['username'] ?? '',
            'layout' => 'app'
        ]);
    }

    public function store()
    {
        // Update EDM credentials
        $edmUsername = $this->request->input('edm_username');
        $edmPassword = $this->request->input('edm_password');

        if (!empty($edmUsername) && !empty($edmPassword)) {
            Session::set('edm_credentials', [
                'username' => $edmUsername,
                'password' => $edmPassword,
                'wsdl' => 'https://portal.edmbilisim.com.tr/EFaturaEDM/EFaturaEDM.svc?singleWsdl'
            ]);

            $this->flash('success', 'Ayarlar kaydedildi!');
        }

        return $this->redirect('/ayarlar');
    }
}
