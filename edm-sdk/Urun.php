<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:53
 */

namespace EFatura;


class Urun
{
    private $serbest_aciklama;
    private $ad;
    private $keyword;
    private $marka;
    private $model;
    private $alici_kod;
    private $satici_kod;
    private $uretici_kod;
    private $emtia_sinif;

    /**
     * @return mixed
     */
    public function getSerbestAciklama()
    {
        return $this->serbest_aciklama;
    }

    /**
     * @param mixed $serbest_aciklama
     */
    public function setSerbestAciklama($serbest_aciklama)
    {
        $this->serbest_aciklama = $serbest_aciklama;
    }

    /**
     * @return mixed
     */
    public function getAd()
    {
        return $this->ad;
    }

    /**
     * @param mixed $ad
     */
    public function setAd($ad)
    {
        $this->ad = $ad;
    }

    /**
     * @return mixed
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param mixed $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return mixed
     */
    public function getMarka()
    {
        return $this->marka;
    }

    /**
     * @param mixed $marka
     */
    public function setMarka($marka)
    {
        $this->marka = $marka;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getAliciKod()
    {
        return $this->alici_kod;
    }

    /**
     * @param mixed $alici_kod
     */
    public function setAliciKod($alici_kod)
    {
        $this->alici_kod = $alici_kod;
    }

    /**
     * @return mixed
     */
    public function getSaticiKod()
    {
        return $this->satici_kod;
    }

    /**
     * @param mixed $satici_kod
     */
    public function setSaticiKod($satici_kod)
    {
        $this->satici_kod = $satici_kod;
    }

    /**
     * @return mixed
     */
    public function getUreticiKod()
    {
        return $this->uretici_kod;
    }

    /**
     * @param mixed $uretici_kod
     */
    public function setUreticiKod($uretici_kod)
    {
        $this->uretici_kod = $uretici_kod;
    }

    /**
     * @return mixed
     */
    public function getEmtiaSinif()
    {
        return $this->emtia_sinif;
    }

    /**
     * @param mixed $emtia_sinif
     */
    public function setEmtiaSinif($emtia_sinif)
    {
        $this->emtia_sinif = $emtia_sinif;
    }

    /**
     * @return mixed
     */
    public function getOzelKod()
    {
        return $this->ozel_kod;
    }

    /**
     * @param mixed $ozel_kod
     */
    public function setOzelKod($ozel_kod)
    {
        $this->ozel_kod = $ozel_kod;
    }

    public function readXML(){
        $xmlStr = '<cac:Item> ';
        if($this->getSerbestAciklama()!=""){
            $xmlStr.='<cbc:Description>'.$this->getSerbestAciklama().'</cbc:Description>';
        }
        if($this->getAd()!=""){
            $xmlStr.='<cbc:Name>'.$this->getAd().'</cbc:Name>';
        }
        if($this->getMarka()!=""){
            $xmlStr.='<cbc:BrandName>'.$this->getMarka().'</cbc:BrandName>';
        }
        if($this->getModel()!=""){
            $xmlStr.='<cbc:ModelName>'.$this->getModel().'</cbc:ModelName>';
        }
        if($this->getAliciKod()!=""){
            $xmlStr.='<cac:BuyersItemIdentification><cbc:ID>'.$this->getAliciKod().'</cbc:ID></cac:BuyersItemIdentification>';
        }
        if($this->getSaticiKod()!=""){
            $xmlStr.='<cac:SellersItemIdentification><cbc:ID>'.$this->getAliciKod().'</cbc:ID></cac:SellersItemIdentification>';
        }
        if($this->getUreticiKod()!=""){
            $xmlStr.='<cac:ManufacturersItemIdentification><cbc:ID>'.$this->getAliciKod().'</cbc:ID></cac:ManufacturersItemIdentification>';
        }
        if($this->getEmtiaSinif()!=""){
            $xmlStr.='<cac:CommodityClassification><cbc:ItemClassificationCode>'.$this->getAliciKod().'</cbc:ItemClassificationCode></cac:CommodityClassification>';
        }
        $xmlStr.='</cac:Item>';
        return $xmlStr;
    }

}