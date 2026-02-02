<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:53
 */

namespace EFatura;


class Taraf
{

    private $webSite;
    private $naceKod;
    private $vergi_daire;
    private $id = array();
    private $unvan;
    private $adres;
    private $ilce;
    private $il;
    private $ulke_kod;
    private $ulke_ad;
    private $postaKod;
    private $telefon;
    private $eposta;
    private $ad;
    private $soyad;

    /**
     * @return mixed
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * @param mixed $webSite
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;
    }

    /**
     * @return mixed
     */
    public function getNaceKod()
    {
        return $this->naceKod;
    }

    /**
     * @param mixed $naceKod
     */
    public function setNaceKod($naceKod)
    {
        $this->naceKod = $naceKod;
    }

    /**
     * @return mixed
     */
    public function getVergiDaire()
    {
        return $this->vergi_daire;
    }

    /**
     * @param mixed $vergi_daire
     */
    public function setVergiDaire($vergi_daire)
    {
        $this->vergi_daire = $vergi_daire;
    }

    /**
     * @return mixed
     */
    public function getUnvan()
    {
        return $this->unvan;
    }

    /**
     * @param mixed $unvan
     */
    public function setUnvan($unvan)
    {
        $this->unvan = $unvan;
    }

    /**
     * @return mixed
     */
    public function getAdres()
    {
        return $this->adres;
    }

    /**
     * @param mixed $adres
     */
    public function setAdres($adres)
    {
        $this->adres = $adres;
    }

    /**
     * @return mixed
     */
    public function getPostaKod()
    {
        return $this->postaKod;
    }

    /**
     * @param mixed $postaKod
     */
    public function setPostaKod($postaKod)
    {
        $this->postaKod = $postaKod;
    }

    /**
     * @return mixed
     */
    public function getIlce()
    {
        return $this->ilce;
    }

    /**
     * @param mixed $ilce
     */
    public function setIlce($ilce)
    {
        $this->ilce = $ilce;
    }

    /**
     * @return mixed
     */
    public function getIl()
    {
        return $this->il;
    }

    /**
     * @param mixed $il
     */
    public function setIl($il)
    {
        $this->il = $il;
    }

    /**
     * @return mixed
     */
    public function getUlkeKod()
    {
        return $this->ulke_kod;
    }

    /**
     * @param mixed $ulke_kod
     */
    public function setUlkeKod($ulke_kod)
    {
        $this->ulke_kod = $ulke_kod;
    }

    /**
     * @return mixed
     */
    public function getUlkeAd()
    {
        return $this->ulke_ad;
    }

    /**
     * @param mixed $ulke_ad
     */
    public function setUlkeAd($ulke_ad)
    {
        $this->ulke_ad = $ulke_ad;
    }

    /**
     * @return mixed
     */
    public function getTelefon()
    {
        return $this->telefon;
    }

    /**
     * @param mixed $telefon
     */
    public function setTelefon($telefon)
    {
        $this->telefon = $telefon;
    }

    /**
     * @return mixed
     */
    public function getEposta()
    {
        return $this->eposta;
    }

    /**
     * @param mixed $eposta
     */
    public function setEposta($eposta)
    {
        $this->eposta = $eposta;
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
    public function getSoyad()
    {
        return $this->soyad;
    }

    /**
     * @param mixed $soyad
     */
    public function setSoyad($soyad)
    {
        $this->soyad = $soyad;
    }

    public function setID($key,$val){
        $this->id[$key] = $val;
    }

    public function getID(){
        return $this->id;
    }

    public function readXML(){
        $xmlStr = '';
        if ($this->getWebsite()!=""){
            $xmlStr.='<cbc:WebsiteURI>'.$this->getWebsite().'</cbc:WebsiteURI>';
        }
        if(count($this->getID())>0){
            foreach ($this->getID() as $key=>$item) {
                $xmlStr.='<cac:PartyIdentification><cbc:ID schemeID="'.$key.'">'.$item.'</cbc:ID></cac:PartyIdentification>';
            }
        }
        $xmlStr.='<cac:PartyName><cbc:Name>'.$this->getUnvan().'</cbc:Name></cac:PartyName>';
        $xmlStr.='<cac:PostalAddress> ';
        $xmlStr.='<cbc:BuildingName>'.$this->getAdres().'</cbc:BuildingName>';
        $xmlStr.='<cbc:CitySubdivisionName>'.$this->getIlce().'</cbc:CitySubdivisionName>';
        $xmlStr.='<cbc:CityName>'.$this->getIl().'</cbc:CityName>';
        if($this->getPostaKod()!=""){
            $xmlStr.='<cbc:PostalZone>'.$this->getPostaKod().'</cbc:PostalZone>';
        }
        $xmlStr.='<cac:Country>';
        $xmlStr.='<cbc:IdentificationCode>'.$this->getUlkeKod().'</cbc:IdentificationCode>';
        $xmlStr.='<cbc:Name>'.$this->getUlkeAd().'</cbc:Name>';
        $xmlStr.='</cac:Country>';
        $xmlStr.='</cac:PostalAddress>';
        $xmlStr.='<cac:PartyTaxScheme><cac:TaxScheme><cbc:Name>'.$this->getVergiDaire().'</cbc:Name></cac:TaxScheme></cac:PartyTaxScheme>';
        $xmlStr.='<cac:Contact>';
        $xmlStr.='<cbc:Telephone>'.$this->getTelefon().'</cbc:Telephone>';
        $xmlStr.='<cbc:ElectronicMail>'.$this->getEposta().'</cbc:ElectronicMail>';
        $xmlStr.='</cac:Contact>';
        if($this->getAd()!="" && $this->getSoyad()!=""){
            $xmlStr.='<cac:Person><cbc:FirstName>'.$this->getAd().'</cbc:FirstName><cbc:FamilyName>'.$this->getSoyad().'</cbc:FamilyName></cac:Person>';
        }
        return $xmlStr;
    }


}