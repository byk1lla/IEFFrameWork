<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:54
 */

namespace EFatura;


class Util
{
    public static $service_url = "";
    public static function GUID()
    {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    public static function actionDate()
    {
        return date("Y-m-d") . "T" . date("H:i:s");
    }
    public static function issueDate()
    {
        return date("Y-m-d");
    }
    public static function issueTime()
    {
        return date("H:i:s");
    }
    public static function formatDecimal($sayi, $basamak = 3)
    {
        return number_format($sayi, $basamak, ".", "");
    }
    public static function invoiceStatus($statusCode)
    {
        $statusCode = str_replace(" ", "", $statusCode);
        $durum = array(
            "PACKAGE-PROCESSING" => array("aciklama" => "Zarflama yapılıyor", "yap" => "BEKLE"),
            "SEND-PROCESSING" => array("aciklama" => "Gönderim İşlemi Devam Ediyor", "yap" => "BEKLE"),
            "SEND-WAIT_GIB_RESPONSE" => array("aciklama" => "Gönderim İşlemi Devam Ediyor", "yap" => "BEKLE"),
            "SEND-WAIT_SYSTEM_RESPONSE" => array("aciklama" => "Gönderim İşlemi Devam Ediyor", "yap" => "BEKLE"),
            "REJECT-PROCESSING" => array("aciklama" => "RED Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "REJECT-WAIT_GIB_RESPONSE" => array("aciklama" => "RED Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "REJECT-WAIT_SYSTEM_RESPONSE" => array("aciklama" => "RED Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "ACCEPT-PROCESSING" => array("aciklama" => "KABUL Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "ACCEPT-WAIT_GIB_RESPONSE" => array("aciklama" => "KABUL Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "ACCEPT-WAIT_SYSTEM_RESPONSE" => array("aciklama" => "Kabul Yanıtı Gönderiliyor", "yap" => "BEKLE"),
            "SEND-WAIT_APPLICATION_RESPONSE" => array("aciklama" => "Yanıt Bekleniyor", "yap" => "BEKLE"),
            "UNKNOWN-UNKNOWN" => array("aciklama" => "Belirsiz Durum - İşlem Devam Ediyor", "yap" => "BEKLE"),
            "RECEIVE-WAIT_SYSTEM_RESPONSE" => array("aciklama" => "Sistem Tarafından Yanıt Bekleniyor", "yap" => "BEKLE"),

            "RECEIVE-SUCCEED" => array("aciklama" => "Temel Fatura Başarı İle Alındı", "yap" => "ICERIAL"),
            "ACCEPT-SUCCEED" => array("aciklama" => "KABUL Yanıtı Gönderildi.", "yap" => "ICERIAL"),

            "SEND-SUCCEED" => array("aciklama" => "Fatura Alıcı Tarafından Onaylandı", "yap" => "KABULET"),
            "ACCEPTED-SUCCEED" => array("aciklama" => "Fatura Onaylandı.", "yap" => "KABULET"),

            "RECEIVE-WAIT_APPLICATION_RESPONSE" => array("aciklama" => "Kabul veya Red Yanıtı Bekleniyor", "yap" => "KABULRED"),
            "ACCEPT-FAILED" => array("aciklama" => "KABUL Yanıtı İletilemedi. Yeniden Gönder", "yap" => "KABULRED"),
            "REJECT-FAILED" => array("aciklama" => "RED Yanıtı İletilemedi. Yeniden Gönderin", "yap" => "KABULRED"),

            "REJECTED-SUCCEED" => array("aciklama" => "Fatura Red Edildi.", "yap" => "REDET"),
            "REJECT-SUCCEED" => array("aciklama" => "RED Yanıtı Gönderildi. Faturayı İptal Edin", "yap" => "REDET"),

            "PACKAGE-FAIL" => array("aciklama" => "Zarflamada Hata Alındı", "yap" => "RESEND"),
            "SEND-FAILED" => array("aciklama" => "Gönderim İşlemi Hatalı Bitti, Gönderilemedi", "yap" => "RESEND"),
            "CANCELLED-SUCCEED" => array("aciklama" => "Fatura İptal Edildi", "yap" => "BEKLE")
        );
        return $durum[trim($statusCode)] ?? array("aciklama" => "Bilinmeyen Durum ($statusCode)", "yap" => "BEKLE");
    }
    public static function localInvoiceStatus($statusCode)
    {
        $durum = array(
            "0" => "EFatura Aktif Değil",
            "1" => "Gönderim Bekliyor",
            "2" => "Gönderildi.Giden Kutusuna Bakın"
        );
        return $durum[$statusCode];
    }
    public static function UBLClear($xmlStr)
    {
        $xmlStr = preg_replace("/<Invoice ([a-z][a-z0-9]*)[^>]*?(\/?)>/i", " <Invoice>", $xmlStr);
        $xmlStr = str_replace(array("cbc:", "cac:", "ext:", "ds:", "xades:"), array("", "", "", "", ""), $xmlStr);
        return $xmlStr;
    }
}