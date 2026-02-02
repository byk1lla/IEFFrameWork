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
     * SDK returns array of associative arrays
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

            // SDK returns array directly - each item is associative array
            if (is_array($outbox) && !isset($outbox['error'])) {
                foreach ($outbox as $inv) {
                    // Parse PAYABLE_AMOUNT which comes as "1234.56 TRY" 
                    $amountStr = $inv['PAYABLE_AMOUNT'] ?? '0';
                    $amount = floatval(preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $amountStr)));

                    // Parse ISSUE_DATE or CDATE
                    $dateStr = $inv['ISSUE_DATE'] ?? $inv['CDATE'] ?? '';
                    $formattedDate = '-';
                    if ($dateStr) {
                        try {
                            $formattedDate = date('d.m.Y', strtotime($dateStr));
                        } catch (\Exception $e) {
                            $formattedDate = $dateStr;
                        }
                    }

                    $faturalar[] = [
                        'id' => $inv['ID'] ?? '-',
                        'uuid' => $inv['UUID'] ?? '',
                        'receiver' => $inv['RECEIVER'] ?? $inv['CUSTOMER'] ?? '-',
                        'date' => $formattedDate,
                        'amount' => $amount,
                        'status' => $inv['STATUS'] ?? 'PENDING',
                        'type' => $inv['INVOICE_TYPE'] ?? 'SATIS'
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
            // Debug log
            $logFile = '/opt/homebrew/var/www/efatura-pro/storage/logs/edm_debug.log';
            if (!file_exists(dirname($logFile)))
                mkdir(dirname($logFile), 0777, true);

            error_log(date('[Y-m-d H:i:s] ') . "VKN Sorgu: $vkn\n", 3, $logFile);

            $result = EdmHelper::queryTaxpayer($vkn);
            EdmHelper::logout();

            error_log(date('[Y-m-d H:i:s] ') . "VKN Sonuç: " . print_r($result, true) . "\n", 3, $logFile);

            $user = null;
            if ($result) {
                if (is_array($result)) {
                    // Check if it's an associative array (single user) or numeric array (list)
                    if (isset($result['IDENTIFIER']) || isset($result->IDENTIFIER)) {
                        $user = $result;
                    } else {
                        $user = $result[0] ?? null;
                    }
                } elseif (is_object($result)) {
                    $user = $result;
                }
            }

            if ($user) {
                // Handle both object and array access
                $identifier = is_object($user) ? ($user->IDENTIFIER ?? $vkn) : ($user['IDENTIFIER'] ?? $vkn);
                $title = is_object($user) ? ($user->TITLE ?? $user->ALIAS ?? '-') : ($user['TITLE'] ?? $user['ALIAS'] ?? '-');
                $alias = is_object($user) ? ($user->ALIAS ?? '') : ($user['ALIAS'] ?? '');
                $type = is_object($user) ? ($user->TYPE ?? '') : ($user['TYPE'] ?? '');

                return $this->json([
                    'success' => true,
                    'mukellef' => [
                        'vkn' => $identifier,
                        'unvan' => $title,
                        'alias' => $alias,
                        'type' => $type
                    ]
                ]);
            }

            return $this->json(['success' => false, 'error' => 'Mükellef bulunamadı']);
        } catch (\Exception $e) {
            error_log(date('[Y-m-d H:i:s] ') . "VKN Hata: " . $e->getMessage() . "\n", 3, $logFile);
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cari ara (autocomplete)
     */
    public function cariAra()
    {
        $query = strtolower($this->request->input('q') ?? '');

        $addressBookPath = dirname(__DIR__, 2) . '/edm-sdk/address_book.json';
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

    /**
     * Fatura Detayı Getir
     */
    public function faturaDetay($uuid)
    {
        if (empty($uuid)) {
            return $this->json(['success' => false, 'error' => 'UUID geçersiz']);
        }

        try {
            $detail = EdmHelper::getInvoiceDetail($uuid);
            EdmHelper::logout();

            if ($detail && !isset($detail['error'])) {
                return $this->json(['success' => true, 'data' => $detail]);
            }

            return $this->json(['success' => false, 'error' => $detail['error'] ?? 'Fatura bulunamadı']);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Ünvan ile Mükellef Ara
     */
    public function mukellefAra()
    {
        $query = $this->request->input('q') ?? '';

        if (empty($query) || strlen($query) < 3) {
            return $this->json(['success' => false, 'error' => 'En az 3 karakter giriniz']);
        }

        try {
            $results = EdmHelper::searchTaxpayerByName($query);
            EdmHelper::logout();

            $formatted = [];
            if ($results && is_array($results)) {
                foreach ($results as $user) {
                    $formatted[] = [
                        'vkn' => is_object($user) ? ($user->IDENTIFIER ?? '') : ($user['IDENTIFIER'] ?? ''),
                        'unvan' => is_object($user) ? ($user->TITLE ?? '') : ($user['TITLE'] ?? ''),
                        'type' => is_object($user) ? ($user->TYPE ?? '') : ($user['TYPE'] ?? ''),
                        'alias' => is_object($user) ? ($user->ALIAS ?? '') : ($user['ALIAS'] ?? '')
                    ];
                }
            }

            return $this->json(['success' => true, 'data' => $formatted]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * PDF İndir
     */
    public function downloadPdf($uuid)
    {
        if (empty($uuid))
            die('UUID gerekli');

        try {
            $pdfContent = EdmHelper::getInvoicePdf($uuid);
            EdmHelper::logout();

            if ($pdfContent) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="fatura_' . $uuid . '.pdf"');
                echo $pdfContent;
                exit;
            } else {
                die('PDF bulunamadı');
            }
        } catch (\Exception $e) {
            die('Hata: ' . $e->getMessage());
        }
    }
}
