<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:52
 */

namespace EFatura;


class Satir
{
    private $sira_no;
    private $birim;
    private $miktar;
    private $satir_toplam   =   0.0;
    private $birim_fiyat    =   0.0;
    private $paraBirimKod;
    private $iskontoArttirim=   null;
    /**
     * @var Vergi $vergi
     */
    private $vergi = array();
    /**
     * @var Urun $urun
     */
    private $urun;

    /**
     * @return mixed
     */
    public function getSiraNo()
    {
        return $this->sira_no;
    }

    /**
     * @param mixed $sira_no
     */
    public function setSiraNo($sira_no)
    {
        $this->sira_no = $sira_no;
    }
    /**
     * @return mixed
     */
    public function getBirim()
    {
        return $this->birim;
    }

    /**
     * @param mixed $birim
     */
    public function setBirim($birim)
    {
        $this->birim = $birim;
    }

    /**
     * @return mixed
     */
    public function getMiktar()
    {
        return $this->miktar;
    }

    /**
     * @param mixed $miktar
     */
    public function setMiktar($miktar)
    {
        $this->miktar = $miktar;
    }

    /**
     * @return float
     */
    public function getSatirToplam()
    {
        return $this->satir_toplam;
    }

    /**
     * @param float $satir_toplam
     */
    public function setSatirToplam($satir_toplam)
    {
        $this->satir_toplam = $satir_toplam;
    }

    /**
     * @return float
     */
    public function getBirimFiyat()
    {
        return $this->birim_fiyat;
    }

    /**
     * @param float $birim_fiyat
     */
    public function setBirimFiyat($birim_fiyat)
    {
        $this->birim_fiyat = $birim_fiyat;
    }

    /**
     * @param mixed $paraBirimKod
     */
    public function setParaBirimKod($paraBirimKod)
    {
        $this->paraBirimKod = $paraBirimKod;
    }

    public function getParaBirimKod()
    {
        return $this->paraBirimKod;
    }
    /**
     * @return Vergi
     */
    public function getVergi($idx=null)
    {
        return ($idx==null) ? $this->vergi[0] : $this->vergi[$idx];
    }

    /**
     * @param Vergi $vergi
     */
    public function setVergi(Vergi $vergi)
    {
        $this->vergi[] = $vergi;
    }

    /**
     * @return Urun
     */
    public function getUrun()
    {
        return $this->urun;
    }

    /**
     * @param Urun $urun
     */
    public function setUrun(Urun $urun)
    {
        $this->urun = $urun;
    }

    /**
     * @return null
     */
    public function getIskontoArttirim()
    {
        return $this->iskontoArttirim;
    }

    /**
     * @param null $iskontoArttirim
     */
    public function setIskontoArttirim(IskontoArttirim $iskontoArttirim)
    {
        $this->iskontoArttirim = $iskontoArttirim;
    }


    public function readXML(){
        $xmlStr = '<cac:InvoiceLine>';
        $xmlStr.='<cbc:ID>'.$this->getSiraNo().'</cbc:ID>';
        $xmlStr.='<cbc:InvoicedQuantity unitCode="'.$this->getBirim().'">'.$this->getMiktar().'</cbc:InvoicedQuantity>';
        $xmlStr.='<cbc:LineExtensionAmount currencyID="'.$this->paraBirimKod.'">'.$this->getSatirToplam().'</cbc:LineExtensionAmount>';
        if(!is_null($this->getIskontoArttirim())){
            $xmlStr.=$this->getIskontoArttirim()->readXML();
        }
        $xmlStr.= $this->getVergi()->readXML();
        $xmlStr.= $this->getUrun()->readXML();
        $xmlStr.='<cac:Price><cbc:PriceAmount currencyID="'.$this->paraBirimKod.'">'.$this->getBirimFiyat().'</cbc:PriceAmount></cac:Price>';
        $xmlStr.='</cac:InvoiceLine>';
        return $xmlStr;
    }

}