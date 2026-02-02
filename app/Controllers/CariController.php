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

        // Cari Önerileri (Son faturalardan)
        $oneriler = EdmHelper::getRecentRecipients(6);

        return $this->view('cari/index', [
            'title' => 'Cariler - E-Fatura Pro',
            'cariler' => $cariler,
            'oneriler' => $oneriler,
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
            'layout' => 'app',
            'vkn' => $this->request->get('vkn'),
            'unvan' => $this->request->get('unvan'),
            'alias' => $this->request->get('alias')
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
            'alias' => $this->request->input('alias'), // New: Tag support
            'grup' => $grup,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save
        file_put_contents($addressBookPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->flash('success', 'Cari başarıyla eklendi!');
        return $this->redirect('/cari');
    }

    /**
     * Cari detay ve Veri Zenginleştirme
     */
    public function show($id)
    {
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        $data = ['cariler' => []];
        $cari = null;

        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true);
            $cari = $data['cariler'][$id] ?? null;
        }

        if (!$cari) {
            return $this->view('cari/show', [
                'title' => 'Cari Detay - E-Fatura Pro',
                'cari' => null,
                'layout' => 'app'
            ]);
        }

        // --- VERİ ZENGİNLEŞTİRME (ENRICHMENT) ---
        // Bilgileri EDM üzerinden sorgula ve güncelle
        try {
            $details = EdmHelper::getRecipientDetails($id);
            if ($details && isset($details->vkn)) {
                $m = $details;
                $updated = false;

                // Update basic info if returned from EDM
                if (!empty($m->unvan) && $m->unvan !== ($cari['unvan'] ?? '')) {
                    $cari['unvan'] = $m->unvan;
                    $updated = true;
                }

                // Fields to sync
                $fields = [
                    'vergi_dairesi' => 'vergi_dairesi',
                    'sehir' => 'sehir',
                    'tip' => 'type',
                    'alias' => 'alias',
                    'kayit_tarihi' => 'system_create_time',
                    'adres' => 'adres'
                ];

                foreach ($fields as $cariKey => $edmKey) {
                    $newValue = is_object($m) ? ($m->$edmKey ?? '') : ($m[$edmKey] ?? '');
                    if (!empty($newValue) && $newValue !== ($cari[$cariKey] ?? '')) {
                        $cari[$cariKey] = $newValue;
                        $updated = true;
                    }
                }

                if ($updated) {
                    $data['cariler'][$id] = $cari;
                    file_put_contents($addressBookPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
        } catch (\Exception $e) {
            // Silently fail enrichment
        }

        return $this->view('cari/show', [
            'title' => 'Cari Detay - E-Fatura Pro',
            'cari' => $cari,
            'layout' => 'app'
        ]);
    }

    /**
     * Cari güncelle
     */
    public function update($id)
    {
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        if (!file_exists($addressBookPath)) {
            $this->redirect('/cari');
            return;
        }

        $data = json_decode(file_get_contents($addressBookPath), true);
        if (!isset($data['cariler'][$id])) {
            $this->redirect('/cari');
            return;
        }

        // Update fields
        $data['cariler'][$id]['unvan'] = $this->request->input('unvan');
        $data['cariler'][$id]['adres'] = $this->request->input('adres');
        $data['cariler'][$id]['telefon'] = $this->request->input('telefon');
        $data['cariler'][$id]['email'] = $this->request->input('email');
        $data['cariler'][$id]['grup'] = $this->request->input('grup', 'Genel');
        $data['cariler'][$id]['updated_at'] = date('Y-m-d H:i:s');

        file_put_contents($addressBookPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->flash('success', 'Cari bilgileri güncellendi!');
        return $this->redirect('/cari/' . $id);
    }
}
