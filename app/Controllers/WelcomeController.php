<?php

namespace App\Controllers;

use App\Core\Controller;

class WelcomeController extends Controller
{
    public function index()
    {
        return $this->view('welcome', [
            'title' => 'IEF Framework',
            'version' => '1.0.0'
        ]);
    }
}
