<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class RaporController extends Controller
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
        return $this->view('raporlar/index', [
            'title' => 'Raporlar - E-Fatura Pro',
            'layout' => 'app'
        ]);
    }
}
