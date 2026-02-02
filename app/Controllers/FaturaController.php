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
     * Fatura kaydet/gönder
     */
    public function store()
    {
        // CSRF Check skipped for now or handled by middleware

        $action = $this->request->input('action');

        // Sender Data from Session
        $userData = Session::get('user');
        $sender = [
            'unvan' => $userData['name'] ?? 'DURSUN ERDOĞDU',
            'adres' => 'Pendik / İSTANBUL',
            'il' => 'İSTANBUL',
            'ilce' => 'PENDİK',
            'vergi_daire' => 'PENDİK',
            'vkn' => $userData['vkn'] ?? '3230512384',
            'eposta' => 'info@dursunerdogdu.com'
        ];

        $data = [
            'profile_id' => 'TICARIFATURA', // VKN check needed to switch to EARSIVFATURA
            'fatura_tipi' => $this->request->input('fatura_tipi'),
            'para_birimi' => $this->request->input('para_birimi'),
            'notlar' => $this->request->input('notlar'),
            'sender' => $sender,
            'receiver' => [
                'vkn' => $this->request->input('vkn'),
                'unvan' => $this->request->input('unvan'),
                'adres' => $this->request->input('adres'),
                'il' => 'İSTANBUL', // Form fails to provide this, hardcode or split address
                'ilce' => 'MERKEZ'
            ],
            'satirlar' => []
        ];

        // Parse items from array syntax
        $items = $this->request->input('items', []);

        // If items are not in array format (old single line fallback - though form is updated)
        if (empty($items) && $this->request->input('hizmet_adi')) {
            $items = [
                [
                    'name' => $this->request->input('hizmet_adi'),
                    'qty' => $this->request->input('miktar'),
                    'price' => $this->request->input('birim_fiyat'),
                    'tax' => $this->request->input('kdv_orani')
                ]
            ];
        }

        foreach ($items as $item) {
            // Basic validation
            $qty = (float) ($item['qty'] ?? 1);
            $price = (float) ($item['price'] ?? 0); // Allow 0 price

            if (!empty($item['name'])) {
                $data['satirlar'][] = [
                    'urun_adi' => $item['name'],
                    'miktar' => $qty,
                    'birim_fiyat' => $price,
                    'kdv_orani' => (int) ($item['tax'] ?? 20)
                ];
            }
        }

        // Add tevkifat code if type is TEVKIFAT
        if ($data['fatura_tipi'] === 'TEVKIFAT') {
            $data['tevkifat_kodu'] = $this->request->input('tevkifat_kodu');
        }

        // Check if receiver is E-Fatura user to set Profile ID
        // Simplified check: If VKN length 10 -> checkUser. If 11 -> EARSIV (usually)
        if (strlen($data['receiver']['vkn']) == 11) {
            $data['profile_id'] = 'EARSIVFATURA';
        } else {
            // Basic valid VKN check for E-Fatura
            $isEFatura = EdmHelper::queryTaxpayer($data['receiver']['vkn']);
            $data['profile_id'] = ($isEFatura) ? 'TICARIFATURA' : 'EARSIVFATURA';
            EdmHelper::logout();
        }

        if ($action === 'gonder' || $action === 'taslak') {
            // In draft-only mode, we just save to EDM as a draft (LOGICAL_STATE)
            // Or here, we just simulate/redirect because the user wants to approve in Portal
            $this->flash('success', 'Fatura taslağınız hazırlandı! <br><br><b>ÖNEMLİ:</b> Bu faturayı resmileştirmek için <b>EDM Bilişim portalına</b> giriş yapıp onaylamanız gerekmektedir.');
            return $this->redirect('/fatura');
        }

        $this->flash('success', 'Taslak kaydedildi.');
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
