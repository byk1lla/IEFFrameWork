<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return $this->view('admin.index', [
            'title' => 'IEF Framework | Dashboard',
            'stats' => [
                'users' => 1250,
                'requests' => '45.2k',
                'uptime' => '99.9%',
                'errors' => 2
            ]
        ]);
    }
}
