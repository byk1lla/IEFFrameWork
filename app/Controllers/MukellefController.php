<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\EdmHelper;

class MukellefController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            $this->redirect('/login');
            exit;
        }

        // Force VKN update for local admin if missing (Easter Egg Fix)
        $user = Session::get('user');
        if (($user['username'] ?? '') === 'byk1lla.local' && empty($user['vkn'])) {
            $user['vkn'] = '56359306232';
            Session::set('user', $user);
        }
    }

    public function index()
    {
        return $this->view('mukellef/index', [
            'title' => 'Mükellef Sorgula - E-Fatura Pro',
            'layout' => 'app',
            'result' => null,
            'results' => []
        ]);
    }

    public function sorgula()
    {
        $input = trim($this->request->input('q') ?? $this->request->input('vkn') ?? '');
        $results = [];
        $error = null;

        if (empty($input)) {
            return $this->index();
        }

        // 1. ÖNCELİK: ADRES DEFTERİ (YEREL CARİLER)
        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true);
            foreach ($data['cariler'] ?? [] as $vk => $cari) {
                $searchStr = strtolower($vk . ' ' . ($cari['unvan'] ?? ''));
                if (strpos($searchStr, strtolower($input)) !== false) {
                    $results[] = [
                        'vkn' => $vk,
                        'unvan' => $cari['unvan'] ?? '',
                        'alias' => $cari['alias'] ?? 'Yerel Kayıt',
                        'type' => $cari['tip'] ?? 'Cari',
                        'is_local' => true
                    ];
                }
            }
        }

        // 2. EDM SORGULAMA (VKN veya İsim)
        $isVkn = is_numeric($input) && (strlen($input) === 10 || strlen($input) === 11);

        try {
            if ($isVkn) {
                // Akıllı zenginleştirme ile tüm detayları çek (Adres, VD vb.)
                $u = EdmHelper::smartEnrich($input);
                if (!empty($u)) {
                    $vkn = is_object($u) ? ($u->vkn ?? $input) : ($u['vkn'] ?? $input);
                    
                    // Duplicate check (Local data takes priority for details)
                    $exists = array_filter($results, fn($r) => $r['vkn'] === $vkn);
                    if (empty($exists)) {
                        $results[] = [
                            'vkn' => $vkn,
                            'unvan' => is_object($u) ? ($u->unvan ?? '') : ($u['unvan'] ?? ''),
                            'adres' => is_object($u) ? ($u->adres ?? '') : ($u['adres'] ?? ''),
                            'vergi_dairesi' => is_object($u) ? ($u->vergi_dairesi ?? '') : ($u['vergi_dairesi'] ?? ''),
                            'sehir' => is_object($u) ? ($u->sehir ?? '') : ($u['sehir'] ?? ''),
                            'alias' => is_object($u) ? ($u->alias ?? '') : ($u['alias'] ?? ''),
                            'type' => is_object($u) ? ($u->tip ?? '') : ($u['tip'] ?? ''),
                            'is_local' => false
                        ];
                    }
                }
            } else {
                // İsimle ara (EDM SDK Cache üzerinden)
                $users = EdmHelper::searchTaxpayerByName($input);
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $vkn = is_object($user) ? ($user->IDENTIFIER ?? '') : ($user['IDENTIFIER'] ?? '');
                        // Duplicate check
                        $exists = array_filter($results, fn($r) => $r['vkn'] === $vkn);
                        if (empty($exists)) {
                            $results[] = [
                                'vkn' => $vkn,
                                'unvan' => is_object($user) ? ($user->TITLE ?? '') : ($user['TITLE'] ?? ''),
                                'alias' => is_object($user) ? ($user->ALIAS ?? '') : ($user['ALIAS'] ?? ''),
                                'type' => is_object($user) ? ($user->TYPE ?? '') : ($user['TYPE'] ?? ''),
                                'is_local' => false
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            if (empty($results))
                $error = 'Sorgulama hatası: ' . $e->getMessage();
        }

        if (empty($results) && !$error) {
            $error = 'Arama kriterine uygun mükellef bulunamadı.';
        }

        $user = Session::get('user');
        $ownVkn = $user['vkn'] ?? '';

        return $this->view('mukellef/index', [
            'title' => 'Mükellef Sorgula - E-Fatura Pro',
            'layout' => 'app',
            'query' => $input,
            'results' => $results,
            'error' => $error,
            'own_vkn' => $ownVkn
        ]);
    }
}
