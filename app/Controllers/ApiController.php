<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\EdmHelper;

/**
 * API Controller - Async data loading endpoints
 */
class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // API auth check
        if (!Session::isLoggedIn()) {
            $this->json(['error' => 'Unauthorized'], 401);
            exit;
        }
    }

    /**
     * Dashboard stats - async loading
     */
    public function dashboardStats()
    {
        try {
            $stats = EdmHelper::getDashboardStats();
            return $this->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Fatura listesi - async loading
     */
    public function faturaListesi()
    {
        try {
            $startDate = $this->request->input('baslangic') ?? date('Y-m-d', strtotime('-90 days'));
            $endDate = $this->request->input('bitis') ?? date('Y-m-d');
            $limit = intval($this->request->input('limit') ?? 100);

            $outbox = EdmHelper::getOutgoingInvoices($startDate, $endDate, $limit);
            EdmHelper::logout();

            $faturalar = [];
            if (is_object($outbox) && isset($outbox->INVOICE)) {
                $invoices = is_array($outbox->INVOICE) ? $outbox->INVOICE : [$outbox->INVOICE];
                foreach ($invoices as $inv) {
                    $faturalar[] = [
                        'id' => $inv->ID ?? '-',
                        'uuid' => $inv->UUID ?? '',
                        'receiver' => $inv->RECEIVER_NAME ?? $inv->ReceiverName ?? '-',
                        'date' => isset($inv->CREATE_DATE) ? date('d.m.Y', strtotime($inv->CREATE_DATE)) : '-',
                        'amount' => floatval($inv->PAYABLE_AMOUNT ?? $inv->PayableAmount ?? 0),
                        'status' => $inv->STATUS ?? $inv->Status ?? 'PENDING',
                        'type' => $inv->INVOICE_TYPE ?? $inv->InvoiceType ?? 'SATIS'
                    ];
                }
            }

            return $this->json(['success' => true, 'data' => $faturalar]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * VKN sorgula
     */
    public function vknSorgula()
    {
        $vkn = $this->request->input('vkn');

        if (empty($vkn) || strlen($vkn) < 10) {
            return $this->json(['success' => false, 'error' => 'Geçersiz VKN']);
        }

        try {
            $result = EdmHelper::queryTaxpayer($vkn);
            EdmHelper::logout();

            if ($result && isset($result->User)) {
                $user = is_array($result->User) ? $result->User[0] : $result->User;
                return $this->json([
                    'success' => true,
                    'mukellef' => [
                        'vkn' => $user->Identifier ?? $vkn,
                        'unvan' => $user->Title ?? $user->Alias ?? '-',
                        'alias' => $user->Alias ?? '',
                        'type' => $user->Type ?? ''
                    ]
                ]);
            }

            return $this->json(['success' => false, 'error' => 'Mükellef bulunamadı']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cari ara (autocomplete)
     */
    public function cariAra()
    {
        $query = strtolower($this->request->input('q') ?? '');

        $addressBookPath = __DIR__ . '/../../edm-sdk/address_book.json';
        $results = [];

        if (file_exists($addressBookPath)) {
            $data = json_decode(file_get_contents($addressBookPath), true);
            $cariler = $data['cariler'] ?? [];

            foreach ($cariler as $vkn => $cari) {
                $searchText = strtolower(($cari['vkn'] ?? '') . ' ' . ($cari['unvan'] ?? ''));
                if (empty($query) || strpos($searchText, $query) !== false) {
                    $results[] = [
                        'vkn' => $vkn,
                        'unvan' => $cari['unvan'] ?? '',
                        'adres' => $cari['adres'] ?? ''
                    ];
                    if (count($results) >= 10)
                        break;
                }
            }
        }

        return $this->json(['success' => true, 'data' => $results]);
    }
}
