<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:51
 */

namespace EFatura;


class Request
{
    public $hataMesaj;
    public $hataKod;
    public function send($func_name, $param)
    {
        try {
            $this->hataKod = "0";
            $this->hataMesaj = "0";
            $istemci = new \SoapClient(Util::$service_url, array('trace' => 1));
            $sonuc = $istemci->__soapCall($func_name, [$param]);
            //var_dump($istemci->__getLastRequest());
            return $sonuc;
        } catch (\SoapFault $hata) {
            $this->hataKod = $hata->faultcode;
            $this->hataMesaj = $hata->faultstring;
            // Debug: show actual request for troubleshooting
            if (isset($istemci)) {
                error_log("SOAP Request: " . $istemci->__getLastRequest());
                error_log("SOAP Response: " . $istemci->__getLastResponse());
            }
            throw new \Exception("Soap Hata : " . $hata->faultstring . " (Detay: " . $hata->getMessage() . ")");
        }
    }
}