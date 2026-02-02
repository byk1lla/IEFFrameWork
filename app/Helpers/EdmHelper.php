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
            require_once dirname(__DIR__, 2) . '/edm-sdk/autoload.php';

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
     * Gelen faturaları getir
     * SDK returns ARRAY directly (not object with INVOICE property)
     */
    public static function getIncomingInvoices($startDate = null, $endDate = null, $limit = 50)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            $startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
            $endDate = $endDate ?? date('Y-m-d');

            // SDK returns array directly!
            return $client->getIncomingInvoice($limit, null, null, $startDate, $endDate);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Giden faturaları getir
     * SDK returns ARRAY directly (not object with INVOICE property)
     */
    public static function getOutgoingInvoices($startDate = null, $endDate = null, $limit = 50)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            $startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
            $endDate = $endDate ?? date('Y-m-d');

            // SDK returns array directly!
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
     * Dashboard istatistikleri
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

            // Gelen faturalar - SDK returns array directly
            $inbox = self::getIncomingInvoices(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 100);
            if (is_array($inbox) && !isset($inbox['error'])) {
                $stats['toplam_gelen'] = count($inbox);
            }

            // Giden faturalar - SDK returns array directly
            $outbox = self::getOutgoingInvoices(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 100);
            if (is_array($outbox) && !isset($outbox['error'])) {
                $stats['toplam_giden'] = count($outbox);

                foreach ($outbox as $inv) {
                    // inv is associative array with keys like PAYABLE_AMOUNT, STATUS, etc.
                    $amountStr = $inv['PAYABLE_AMOUNT'] ?? '0';
                    $amount = floatval(preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $amountStr)));
                    $stats['aylık_ciro'] += $amount;

                    $status = $inv['STATUS'] ?? '';
                    if (stripos($status, 'SUCCEED') !== false || stripos($status, 'APPROVED') !== false) {
                        $stats['onaylanan']++;
                    } else {
                        $stats['bekleyen']++;
                    }
                }

                // Son 5 fatura
                $stats['son_faturalar'] = array_slice($outbox, 0, 5);
            }

            self::logout();

        } catch (\Exception $e) {
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    /**
     * Tekil Fatura Detayı Getir
     */
    public static function getInvoiceDetail($uuid)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            // Try outgoing first
            $result = $client->getSingleInvoice(null, $uuid, false, "XML");

            // If empty or error, try incoming
            if (!$result || (is_array($result) && empty($result))) {
                $result = $client->getSingleInvoice(null, $uuid, true, "XML");
            }

            return $result;

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
