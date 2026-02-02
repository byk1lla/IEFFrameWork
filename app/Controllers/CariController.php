<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\EdmHelper;

class CariController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            $this->redirect('/login');
            exit;
        }
    }

    /**
     * Cari listesi
     */
    public function index()
    {
        // Load address book from JSON
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        $cariler = [];

        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true);
            $cariler = $data['cariler'] ?? [];
        }

        return $this->view('cari/index', [
            'title' => 'Cariler - E-Fatura Pro',
            'cariler' => $cariler,
            'layout' => 'app'
        ]);
    }

    /**
     * Yeni cari formu
     */
    public function create()
    {
        return $this->view('cari/create', [
            'title' => 'Yeni Cari - E-Fatura Pro',
            'layout' => 'app'
        ]);
    }

    /**
     * Cari kaydet
     */
    public function store()
    {
        $vkn = $this->request->input('vkn');
        $unvan = $this->request->input('unvan');
        $adres = $this->request->input('adres');
        $telefon = $this->request->input('telefon');
        $email = $this->request->input('email');
        $grup = $this->request->input('grup', 'Genel');

        // Load existing
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        $data = ['cariler' => []];

        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true) ?: ['cariler' => []];
        }

        // Add new
        $data['cariler'][$vkn] = [
            'vkn' => $vkn,
            'unvan' => $unvan,
            'adres' => $adres,
            'telefon' => $telefon,
            'email' => $email,
            'grup' => $grup,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save
        file_put_contents($addressBookPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->flash('success', 'Cari başarıyla eklendi!');
        return $this->redirect('/cari');
    }

    /**
     * Cari detay
     */
    public function show($id)
    {
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        $cari = null;

        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true);
            $cari = $data['cariler'][$id] ?? null;
        }

        return $this->view('cari/show', [
            'title' => 'Cari Detay - E-Fatura Pro',
            'cari' => $cari,
            'layout' => 'app'
        ]);
    }
}
