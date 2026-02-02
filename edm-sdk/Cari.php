<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:48
 */

namespace EFatura;


class Cari
{
    private $website;
    private $vergi_daire;
    private $vkn;
    private $mersisno;
    private $hizmetno;
    private $ticaret_sicil_no;
    private $unvan;
    private $adres;
    private $ilce;
    private $il;
    private $ulke_kod;
    private $ulke_ad;
    private $telefon;
    private $eposta;
    private $tip = "TUZELKISI";
    private $gibUrn;
    private $pozisyon;
    private $tckn;
    private $ad;
    private $soyad;

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
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
    public function getVkn()
    {
        return $this->vkn;
    }

    /**
     * @param mixed $vkn
     */
    public function setVkn($vkn)
    {
        $this->vkn = $vkn;
    }

    /**
     * @return mixed
     */
    public function getTckn()
    {
        return $this->tckn;
    }

    /**
     * @param mixed $tckn
     */
    public function setTckn($tckn)
    {
        $this->tckn = $tckn;
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

    /**
     * @return mixed
     */
    public function getMersisno()
    {
        return $this->mersisno;
    }

    /**
     * @param mixed $mersisno
     */
    public function setMersisno($mersisno)
    {
        $this->mersisno = $mersisno;
    }

    /**
     * @return mixed
     */
    public function getHizmetno()
    {
        return $this->hizmetno;
    }

    /**
     * @param mixed $hizmetno
     */
    public function setHizmetno($hizmetno)
    {
        $this->hizmetno = $hizmetno;
    }

    /**
     * @return mixed
     */
    public function getTicaretSicilNo()
    {
        return $this->ticaret_sicil_no;
    }

    /**
     * @param mixed $ticaret_sicil_no
     */
    public function setTicaretSicilNo($ticaret_sicil_no)
    {
        $this->ticaret_sicil_no = $ticaret_sicil_no;
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
    public function getPozisyon()
    {
        return $this->pozisyon;
    }

    /**
     * @param mixed $pozisyon
     */
    public function setPozisyon($pozisyon)
    {
        $this->pozisyon = $pozisyon;
    }

    /**
     * @return mixed
     */
    public function getGibUrn()
    {
        return $this->gibUrn;
    }

    /**
     * @param mixed $gibUrn
     */
    public function setGibUrn($gibUrn)
    {
        $this->gibUrn = $gibUrn;
    }


    public function readXML(){
        if($this->getPozisyon()=="GONDEREN"){
            $xmlStr = '<cac:AccountingSupplierParty>';
        }else{
            $xmlStr = '<cac:AccountingCustomerParty>';
        }
        $xmlStr.='<cac:Party> ';
        if ($this->getWebsite()!=""){
            $xmlStr.='<cbc:WebsiteURI>'.$this->getWebsite().'</cbc:WebsiteURI>';
        }
        switch($this->getTip()){
            case "TUZELKISI":
                if ($this->getVkn()!=""){
                    $xmlStr.='<cac:PartyIdentification><cbc:ID schemeID="VKN">'.$this->getVkn().'</cbc:ID></cac:PartyIdentification>';
                }
                if ($this->getMersisno()!=""){
                    $xmlStr.='<cac:PartyIdentification><cbc:ID schemeID="MERSISNO">'.$this->getMersisno().'</cbc:ID></cac:PartyIdentification>';
                }
                if ($this->getHizmetno()!=""){
                    $xmlStr.='<cac:PartyIdentification><cbc:ID schemeID="HIZMETNO">'.$this->getHizmetno().'</cbc:ID></cac:PartyIdentification>';
                }
                if($this->getUnvan()!=""){
                    $xmlStr.='<cac:PartyName><cbc:Name>'.$this->getUnvan().'</cbc:Name></cac:PartyName>';
                }
                break;
            case "GERCEKKISI":
                if($this->getTckn()!=""){
                    $xmlStr.='<cac:PartyIdentification><cbc:ID schemeID="TCKN">'.$this->getTckn().'</cbc:ID></cac:PartyIdentification>';
                }
                if($this->getUnvan()!=""){
                    $xmlStr.='<cac:PartyName><cbc:Name>'.$this->getUnvan().'</cbc:Name></cac:PartyName>';
                }
                break;
        }

        $xmlStr.='<cac:PostalAddress> ';
        $xmlStr.='<cbc:BuildingName>'.$this->getAdres().'</cbc:BuildingName>';
        $xmlStr.='<cbc:CitySubdivisionName>'.$this->getIlce().'</cbc:CitySubdivisionName>';
        $xmlStr.='<cbc:CityName>'.$this->getIl().'</cbc:CityName>';
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
        if($this->getTip()=="GERCEKKISI"){
            if($this->getAd()!="" && $this->getSoyad()!=""){
                $xmlStr.='<cac:Person><cbc:FirstName>'.$this->getAd().'</cbc:FirstName><cbc:FamilyName>'.$this->getSoyad().'</cbc:FamilyName></cac:Person>';
            }

        }

        $xmlStr.='</cac:Party>';
        if($this->getPozisyon()=="GONDEREN"){
            $xmlStr.='</cac:AccountingSupplierParty>';
        }else{
            $xmlStr.='</cac:AccountingCustomerParty>';
        }
        return $xmlStr;
    }

}