<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:50
 */

namespace EFatura;


class OdemeSekli
{
    private $kod = "1";
    private $tarih;
    private $hesapKod = "";
    private $paraBirimKod;
    private $odemeNot;

    /**
     * @return string
     */
    public function getKod()
    {
        return $this->kod;
    }

    /**
     * @param string $kod
     */
    public function setKod($kod)
    {
        $this->kod = $kod;
    }

    /**
     * @return mixed
     */
    public function getTarih()
    {
        return $this->tarih;
    }

    /**
     * @param mixed $tarih
     */
    public function setTarih($tarih)
    {
        $this->tarih = $tarih;
    }

    /**
     * @return string
     */
    public function getHesapKod()
    {
        return $this->hesapKod;
    }

    /**
     * @param string $hesapKod
     */
    public function setHesapKod($hesapKod)
    {
        $this->hesapKod = $hesapKod;
    }

    /**
     * @return mixed
     */
    public function getParaBirimKod()
    {
        return $this->paraBirimKod;
    }

    /**
     * @param mixed $paraBirimKod
     */
    public function setParaBirimKod($paraBirimKod)
    {
        $this->paraBirimKod = $paraBirimKod;
    }

    /**
     * @return mixed
     */
    public function getOdemeNot()
    {
        return $this->odemeNot;
    }

    /**
     * @param mixed $odemeNot
     */
    public function setOdemeNot($odemeNot)
    {
        $this->odemeNot = $odemeNot;
    }

    public function readXML(){
        $xmlStr = '<cac:PaymentMeans>';
        $xmlStr.='<cbc:PaymentMeansCode>'.$this->getKod().'</cbc:PaymentMeansCode>';
        $xmlStr.='<cbc:PaymentDueDate>'.$this->getTarih().'</cbc:PaymentDueDate>';
        $xmlStr.='<cac:PayeeFinancialAccount>';
        $xmlStr.='<cbc:ID>'.$this->getHesapKod().'</cbc:ID>';
        $xmlStr.='<cbc:CurrencyCode>'.$this->getParaBirimKod().'</cbc:CurrencyCode>';
        $xmlStr.='<cbc:PaymentNote>'.$this->getOdemeNot().'</cbc:PaymentNote>';
        $xmlStr.='</cac:PayeeFinancialAccount>';
        $xmlStr.= '</cac:PaymentMeans>';
        return $xmlStr;
    }
}