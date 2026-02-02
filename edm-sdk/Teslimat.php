<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:53
 */

namespace EFatura;


class Teslimat
{
    private $kargoAd;
    private $gonderimTarih;
    private $ulkeKod;
    private $ulkeAd;

    /**
     * @return mixed
     */
    public function getKargoAd()
    {
        return $this->kargoAd;
    }

    /**
     * @param mixed $kargoAd
     */
    public function setKargoAd($kargoAd)
    {
        $this->kargoAd = $kargoAd;
    }

    /**
     * @return mixed
     */
    public function getGonderimTarih()
    {
        return $this->gonderimTarih;
    }

    /**
     * @param mixed $gonderimTarih
     */
    public function setGonderimTarih($gonderimTarih)
    {
        $this->gonderimTarih = $gonderimTarih;
    }

    /**
     * @return mixed
     */
    public function getUlkeKod()
    {
        return $this->ulkeKod;
    }

    /**
     * @param mixed $ulkeKod
     */
    public function setUlkeKod($ulkeKod)
    {
        $this->ulkeKod = $ulkeKod;
    }

    /**
     * @return mixed
     */
    public function getUlkeAd()
    {
        return $this->ulkeAd;
    }

    /**
     * @param mixed $ulkeAd
     */
    public function setUlkeAd($ulkeAd)
    {
        $this->ulkeAd = $ulkeAd;
    }



    public function getKargo(){
        $kargo  =   new Taraf();
        return $kargo;
    }

    public function readXML(){
        $kargo  =   $this->getKargo();
        $kargo->setID("VKN","1234567890");
        $kargo->setUnvan($this->getKargoAd());
        $kargo->setUlkeKod($this->getUlkeKod());
        $kargo->setUlkeAd($this->getUlkeAd());
        $xmlStr = '<cac:Delivery>';
        $xmlStr.='<cac:CarrierParty>';
        $xmlStr.=$kargo->readXML();
        $xmlStr.='</cac:CarrierParty>';
        $xmlStr.='<cac:Despatch><cbc:ActualDespatchDate>'.$this->getGonderimTarih().'</cbc:ActualDespatchDate></cac:Despatch>';
        $xmlStr.= '</cac:Delivery>';
        return $xmlStr;
    }



}