<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:50
 */

namespace EFatura;


class IskontoArttirim
{
    private $tip;
    private $sira;
    private $sebep;
    private $oran;
    private $tutar;
    private $uygTutar;
    private $parabirimKod;

    /**
     * @return mixed
     */
    public function getTip()
    {
        return $this->tip;
    }

    /**
     * @param mixed $tip
     */
    public function setTip($tip)
    {
        $this->tip = $tip;
    }

    /**
     * @return mixed
     */
    public function getSira()
    {
        return $this->sira;
    }

    /**
     * @param mixed $sira
     */
    public function setSira($sira)
    {
        $this->sira = $sira;
    }

    /**
     * @return mixed
     */
    public function getSebep()
    {
        return $this->sebep;
    }

    /**
     * @param mixed $sebep
     */
    public function setSebep($sebep)
    {
        $this->sebep = $sebep;
    }

    /**
     * @return mixed
     */
    public function getOran()
    {
        return $this->oran;
    }

    /**
     * @param mixed $oran
     */
    public function setOran($oran)
    {
        $this->oran = $oran;
    }

    /**
     * @return mixed
     */
    public function getTutar()
    {
        return $this->tutar;
    }

    /**
     * @param mixed $tutar
     */
    public function setTutar($tutar)
    {
        $this->tutar = $tutar;
    }

    /**
     * @return mixed
     */
    public function getUygTutar()
    {
        return $this->uygTutar;
    }

    /**
     * @param mixed $uygTutar
     */
    public function setUygTutar($uygTutar)
    {
        $this->uygTutar = $uygTutar;
    }

    /**
     * @return mixed
     */
    public function getParabirimKod()
    {
        return $this->parabirimKod;
    }

    /**
     * @param mixed $parabirimKod
     */
    public function setParabirimKod($parabirimKod)
    {
        $this->parabirimKod = $parabirimKod;
    }



    public function readXML(){
        $xmlStr = '<cac:AllowanceCharge>';
        $xmlStr.='<cbc:ChargeIndicator>'.$this->getTip().'</cbc:ChargeIndicator>';
        if($this->getSebep()!=""){
            $xmlStr.='<cbc:AllowanceChargeReason>'.$this->getSebep().'</cbc:AllowanceChargeReason>';
        }
        if($this->getOran()!="") {
            $xmlStr .= '<cbc:MultiplierFactorNumeric>' . ($this->getOran() / 100) . '</cbc:MultiplierFactorNumeric>';
        }
        if($this->getUygTutar()!="") {
            $xmlStr .= '<cbc:BaseAmount currencyID="' . $this->getParabirimKod() . '">' . $this->getUygTutar() . '</cbc:BaseAmount>';
        }
        $xmlStr.='<cbc:Amount currencyID="'.$this->getParabirimKod().'">'.$this->getTutar().'</cbc:Amount>';
        $xmlStr.='</cac:AllowanceCharge>';
        return $xmlStr;
    }

}