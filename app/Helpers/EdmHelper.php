<?php

namespace App\Helpers;

/**
 * EDM E-Fatura SDK Helper
 * Mevcut EDM SDK'yı IEF Framework'e entegre eder
 */
class EdmHelper
{
    private static $client = null;
    private static $loggedIn = false;
    private static $username = null;
    private static $password = null;

    /**
     * EDM Client instance al
     */
    public static function getClient()
    {
        if (self::$client === null) {
            require_once __DIR__ . '/../../edm-sdk/autoload.php';

            $credentials = \App\Core\Session::get('edm_credentials');
            if (!$credentials) {
                throw new \Exception('EDM bilgileri bulunamadı. Lütfen tekrar giriş yapın.');
            }

            self::$client = new \EFatura\Client($credentials['wsdl']);
            self::$username = $credentials['username'];
            self::$password = $credentials['password'];
        }

        return self::$client;
    }

    /**
     * EDM'e giriş yap
     */
    public static function login(): bool
    {
        try {
            $client = self::getClient();
            self::$loggedIn = $client->login(self::$username, self::$password);
            return self::$loggedIn;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Çıkış yap
     */
    public static function logout(): void
    {
        if (self::$client && self::$loggedIn) {
            try {
                self::$client->logout();
            } catch (\Exception $e) {
            }
        }
        self::$client = null;
        self::$loggedIn = false;
    }

    /**
     * Gelen faturaları getir (CORRECT METHOD NAME)
     */
    public static function getIncomingInvoices($startDate = null, $endDate = null, $limit = 50)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            $startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
            $endDate = $endDate ?? date('Y-m-d');

            // Correct method: getIncomingInvoice
            return $client->getIncomingInvoice($limit, null, null, $startDate, $endDate);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Giden faturaları getir (CORRECT METHOD NAME)
     */
    public static function getOutgoingInvoices($startDate = null, $endDate = null, $limit = 50)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            $startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
            $endDate = $endDate ?? date('Y-m-d');

            // Correct method: getOutgoingInvoice
            return $client->getOutgoingInvoice($limit, $startDate, $endDate);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * VKN sorgula
     */
    public static function queryTaxpayer($vkn)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            return $client->checkUser($vkn);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Dashboard istatistikleri (async için API endpoint'e taşınacak)
     */
    public static function getDashboardStats()
    {
        $stats = [
            'toplam_gelen' => 0,
            'toplam_giden' => 0,
            'bekleyen' => 0,
            'onaylanan' => 0,
            'aylık_ciro' => 0,
            'son_faturalar' => []
        ];

        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            // Gelen faturalar
            $inbox = self::getIncomingInvoices(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 100);
            if (is_object($inbox) && isset($inbox->INVOICE)) {
                $invoices = is_array($inbox->INVOICE) ? $inbox->INVOICE : [$inbox->INVOICE];
                $stats['toplam_gelen'] = count($invoices);
            }

            // Giden faturalar
            $outbox = self::getOutgoingInvoices(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 100);
            if (is_object($outbox) && isset($outbox->INVOICE)) {
                $invoices = is_array($outbox->INVOICE) ? $outbox->INVOICE : [$outbox->INVOICE];
                $stats['toplam_giden'] = count($invoices);

                // Son 5 fatura ve istatistikler
                $limited = array_slice($invoices, 0, 5);
                foreach ($invoices as $inv) {
                    $amount = floatval($inv->PAYABLE_AMOUNT ?? $inv->PayableAmount ?? 0);
                    $stats['aylık_ciro'] += $amount;

                    $status = $inv->STATUS ?? $inv->Status ?? '';
                    if (stripos($status, 'SUCCEED') !== false || stripos($status, 'APPROVED') !== false) {
                        $stats['onaylanan']++;
                    } else {
                        $stats['bekleyen']++;
                    }
                }

                $stats['son_faturalar'] = $limited;
            }

            self::logout();

        } catch (\Exception $e) {
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }
}
