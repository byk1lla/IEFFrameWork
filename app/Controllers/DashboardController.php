<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // Auth check
        if (!Session::isLoggedIn()) {
            return $this->redirect('/login');
        }

        // Dashboard now loads stats async via HTMX
        // No need to wait for EDM API here - instant page load!
        return $this->view('dashboard/index', [
            'title' => 'Dashboard - E-Fatura Pro',
            'stats' => [], // Stats load async
            'layout' => 'app'
        ]);
    }
}
