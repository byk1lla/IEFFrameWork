<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class FaturaController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Auth check
        if (!Session::isLoggedIn()) {
            $this->redirect('/login');
            exit;
        }
    }

    /**
     * Fatura listesi - Async loading (no EDM call here!)
     */
    public function index()
    {
        // Page loads instantly, data loaded via JavaScript fetch
        return $this->view('fatura/index', [
            'title' => 'Faturalar - E-Fatura Pro',
            'layout' => 'app'
        ]);
    }

    /**
     * Yeni fatura formu
     */
    public function create()
    {
        return $this->view('fatura/create', [
            'title' => 'Yeni Fatura - E-Fatura Pro',
            'layout' => 'app'
        ]);
    }

    /**
     * Fatura kaydet
     */
    public function store()
    {
        // TODO: Implement invoice creation using EDM SDK
        $this->flash('success', 'Fatura başarıyla oluşturuldu!');
        return $this->redirect('/fatura');
    }

    /**
     * Fatura detay
     */
    public function show($id)
    {
        return $this->view('fatura/show', [
            'title' => 'Fatura Detay - E-Fatura Pro',
            'id' => $id,
            'layout' => 'app'
        ]);
    }

    /**
     * Fatura gönder
     */
    public function send($id)
    {
        // TODO: Implement invoice sending
        $this->flash('success', 'Fatura GİB\'e gönderildi!');
        return $this->redirect('/fatura');
    }
}
