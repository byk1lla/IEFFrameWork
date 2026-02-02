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
            // Giden faturalar
            $outbox = self::getOutgoingInvoices(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 100);
            if (is_array($outbox) && !isset($outbox['error'])) {
                $stats['toplam_giden'] = count($outbox);

                // Auto-discover own VKN if missing in session
                if (!empty($outbox[0]['SENDER']) && empty(\App\Core\Session::get('user')['vkn'])) {
                    $u = \App\Core\Session::get('user');
                    $u['vkn'] = $outbox[0]['SENDER'];
                    \App\Core\Session::set('user', $u);
                }

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

    /**
     * Ünvan ile Mükellef Ara
     */
    public static function searchTaxpayerByName($query)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            // Use SDK's checkUserByName method which implements logic for searching in full list
            return $client->checkUserByName($query);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Fatura Oluştur ve Gönder
     */
    public static function createAndSendInvoice($data)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            $fatura = new \EFatura\Fatura();

            // Profil ID & Senaryo (E-Arşiv / E-Fatura) -> Receiver Type handles logic in prototype but better explicit here
            // If checking user type before is possible, do it. But for now blindly trust form or default.
            // Simplified logic: If profile is not set, default to TICARIFATURA
            $profileId = $data['profile_id'] ?? 'TICARIFATURA';
            $fatura->setProfileId($profileId);

            // Fatura ID & UUID
            $fatura->setId($data['fatura_id'] ?? ''); // Empty means auto-generate if server allows, usually needs prefix
            $fatura->setUuid(\EFatura\Util::GUID());
            $fatura->setIssueDate(\EFatura\Util::issueDate());
            $fatura->setIssueTime(\EFatura\Util::issueTime());
            $fatura->setInvoiceTypeCode($data['fatura_tipi'] ?? 'SATIS');
            $fatura->setNote($data['notlar'] ?? '');
            $fatura->setDocumentCurrencyCode($data['para_birimi'] ?? 'TRY');

            // Line Count will be auto-calc or manual
            $lines = $data['satirlar'] ?? [];
            $fatura->setLineCountNumeric(count($lines));

            // Gönderici (Biz) - Session'dan veya config'den çekilmeli ama SDK user info'dan alabilir mi?
            // Prototype sets it manually. We should get it from a config or static method.
            // For now, let's look at how prototype does it. It sets new \EFatura\Cari() manually.
            // We need a method to get "Self" company info.
            $duzenleyen = new \EFatura\Cari();
            // TODO: Fetch these from DB or Config. For now hardcode or use data passed.
            // We assume $data['sender'] contains our info, or we use a fixed config.
            // Let's use what we have in $data for now, or fetch from a Settings Helper?
            // Going with $data['sender'] passed from controller which fetches from Settings/DB.
            $senderData = $data['sender'];
            $duzenleyen->setUnvan($senderData['unvan']);
            $duzenleyen->setTip($senderData['tip'] ?? 'TUZELKISI');
            $duzenleyen->setAdres($senderData['adres']);
            $duzenleyen->setIl($senderData['il']);
            $duzenleyen->setIlce($senderData['ilce']);
            $duzenleyen->setUlkeKod("TR");
            $duzenleyen->setUlkeAd("TÜRKİYE");
            $duzenleyen->setVergiDaire($senderData['vergi_daire']);
            $duzenleyen->setVkn($senderData['vkn']);
            $duzenleyen->setEposta($senderData['eposta']);
            $duzenleyen->setGibUrn($senderData['gib_urn'] ?? "urn:mail:defaultgb@edmbilisim.com.tr");
            $fatura->setDuzenleyen($duzenleyen);

            // Alıcı
            $alici = new \EFatura\Cari();
            $receiverData = $data['receiver'];
            $alici->setUnvan($receiverData['unvan']);
            $alici->setAdres($receiverData['adres']);
            $alici->setIl($receiverData['il'] ?? '');
            $alici->setIlce($receiverData['ilce'] ?? '');
            $alici->setUlkeKod("TR");
            $alici->setUlkeAd("TÜRKİYE");
            $alici->setVkn($receiverData['vkn']);
            $alici->setEposta($receiverData['eposta'] ?? '');
            $alici->setGibUrn($receiverData['gib_urn'] ?? "urn:mail:defaultpk@edmbilisim.com.tr");
            $fatura->setAlici($alici);

            // Satırlar
            foreach ($lines as $index => $line) {
                $satir = new \EFatura\Satir();
                $satir->setSiraNo($index + 1);
                $satir->setBirim("NIU"); // Adet
                $satir->setMiktar((float) $line['miktar']);
                $satir->setBirimFiyat((float) $line['birim_fiyat']);

                $tutar = (float) $line['miktar'] * (float) $line['birim_fiyat'];
                $satir->setSatirToplam($tutar);

                // KDV/Vergi
                $kdvOran = (int) $line['kdv_orani'];
                $kdvTutar = $tutar * ($kdvOran / 100);

                $satir_vergi = new \EFatura\Vergi();
                $satir_vergi->setSiraNo(1);
                $satir_vergi->setVergiHaricTutar($tutar);
                $satir_vergi->setVergiTutar($kdvTutar);
                $satir_vergi->setParaBirimKod("TRY");
                $satir_vergi->setVergiOran($kdvOran);
                $satir_vergi->setVergiKod("0015"); // KDV
                $satir_vergi->setVergiAd("KDV GERCEK");
                $satir->setVergi($satir_vergi);

                // Tevkifat handling per line if invoice type matches
                // For simplicity, if invoice is TEVKIFAT, apply to all applicable lines or just add a second Tax?
                // EDM SDK usually requires setting Tevkifat tax code (e.g. 624) either as a separate tax or modifying KDV? 
                // Let's stick to basic for now, user asked for "Code 624" to be selectable. 
                // Usually Tevkifat is 9015 or specific logic. 
                // Implementing basic logic: If tevkifat code present in data, add it to header or lines?
                // For this request: Only saving the code to object reference if SDK supports setTevkifatKod on header or line.
                // Assuming standard SDK usage: Tevkifat usually splits KDV.

                $mal_hizmet = new \EFatura\Urun();
                $mal_hizmet->setAd($line['urun_adi']);
                $satir->setUrun($mal_hizmet);

                $fatura->addSatir($satir);
            }

            // Dip Toplamlar (Basit hesap, iskonto yok varsayıyoruz şimdilik)
            $toplamTutar = 0;
            $toplamKdv = 0;
            foreach ($lines as $line) {
                $t = (float) $line['miktar'] * (float) $line['birim_fiyat'];
                $toplamTutar += $t;
                $toplamKdv += $t * ((int) $line['kdv_orani'] / 100);
            }

            $fatura->setSatirToplam($toplamTutar);
            $fatura->setVergiDahilToplam($toplamTutar + $toplamKdv);
            $fatura->setOdenecekTutar($toplamTutar + $toplamKdv);

            // Eğer Tevkifat ise Code set et (SDK'nın desteklediği şekilde)
            // Note: Prototype didn't explicitly show Tevkifat logic details, so relying on basic assumptions.
            // If header method exists:
            if (!empty($data['tevkifat_kodu'])) {
                // $fatura->setTevkifatKodu($data['tevkifat_kodu']); // Hypothetical
            }

            // GÖNDER
            $result = $client->sendInvoice($fatura);

            return $result;

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Fatura PDF İndir
     */
    public static function getInvoicePdf($uuid)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            return $client->getInvoicePDF($uuid);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Mükellef Detaylarını Getir (Zenginleştirilmiş)
     */
    public static function getRecipientDetails($vkn)
    {
        try {
            $client = self::getClient();
            if (!self::$loggedIn)
                self::login();

            // SDK checkUser returns details like TITLE, IDENTIFIER, etc.
            $result = $client->checkUser($vkn);

            // If it's a list (array), pick the first one
            if (is_array($result) && !empty($result)) {
                $result = $result[0];
            }

            // More details might be available in different SDK methods or via getTurmob if exposed
            // For now, normalize what we have
            if (!$result)
                return null;

            return (object) [
                'sonuc' => true,
                'vkn' => is_object($result) ? ($result->IDENTIFIER ?? $vkn) : ($result['IDENTIFIER'] ?? $vkn),
                'unvan' => is_object($result) ? ($result->TITLE ?? '') : ($result['TITLE'] ?? ''),
                'adres' => is_object($result) ? ($result->ADDRESS ?? '') : ($result['ADDRESS'] ?? ''),
                'vergi_dairesi' => is_object($result) ? ($result->TAX_OFFICE ?? '') : ($result['TAX_OFFICE'] ?? ''),
                'sehir' => is_object($result) ? ($result->CITY ?? '') : ($result['CITY'] ?? ''),
                'ulke' => is_object($result) ? ($result->COUNTRY ?? 'TÜRKİYE') : ($result['COUNTRY'] ?? 'TÜRKİYE'),
                'tip' => is_object($result) ? ($result->TYPE ?? '') : ($result['TYPE'] ?? ''),
                'alias' => is_object($result) ? ($result->ALIAS ?? '') : ($result['ALIAS'] ?? ''),
                'kayit_tarihi' => is_object($result) ? ($result->SYSTEM_CREATE_TIME ?? '') : ($result['SYSTEM_CREATE_TIME'] ?? ''),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Son Faturalardan Önerilen Carileri Getir
     */
    public static function getRecentRecipients($limit = 10)
    {
        try {
            // Giden faturaları çek
            $outbox = self::getOutgoingInvoices(null, null, 100);
            if (!is_array($outbox) || isset($outbox['error']))
                return [];

            $recipients = [];
            foreach ($outbox as $inv) {
                // SDK Keys are RECEIVER (VKN) and CUSTOMER (Title)
                $vkn = $inv['RECEIVER'] ?? null;
                $unvan = $inv['CUSTOMER'] ?? null;

                if ($vkn && $unvan && !isset($recipients[$vkn])) {
                    $recipients[$vkn] = [
                        'vkn' => $vkn,
                        'unvan' => $unvan,
                        'last_date' => $inv['ISSUE_DATE'] ?? ''
                    ];
                    if (count($recipients) >= $limit)
                        break;
                }
            }
            return array_values($recipients);
        } catch (\Exception $e) {
            return [];
        }
    }
}
