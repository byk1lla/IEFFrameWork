<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:49
 */

namespace EFatura;


class Client
{
    private $session_id = null;
    private $hata;
    private $username;
    private $password;

    /**
     * Client constructor.
     * @param string $service_url
     */
    public function __construct($service_url)
    {
        Util::$service_url = $service_url;
    }

    /**
     * @return mixed
     */
    public function getHata()
    {
        return $this->hata;
    }

    /**
     * @param mixed $hata
     */
    public function setHata($hataKod, $hataMesaj)
    {
        $this->hata = array(
            "KOD" => $hataKod,
            "MESAJ" => $hataMesaj
        );
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        if (!is_null($this->session_id)) {
            return $this->session_id;
        }
        return (isset($_SESSION["EFATURA_SESSION"]) ? $_SESSION["EFATURA_SESSION"] : null);
    }

    /**
     * @param mixed $session_id
     */
    public function setSessionId($session_id)
    {
        $_SESSION["EFATURA_SESSION"] = $session_id;
        $this->session_id = $session_id;
    }

    /**
     * @param $username
     * @param $password
     * @return boolean
     */
    public function login($username, $password)
    {
        $header = new RequestHeader();
        $header->session_id = "-1";
        $param = $header->getArray();
        $param["USER_NAME"] = $username;
        $param["PASSWORD"] = $password;
        $request = new Request();
        $session = $request->send("Login", $param);
        if ($session->SESSION_ID != "") {
            $this->setSessionId($session->SESSION_ID);
            $this->username = $username;
            $this->password = $password;
            return true;
        } else {
            $this->setHata($request->hataKod, $request->hataMesaj);
            return false;
        }
    }

    public function logout()
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $param = $req_header->getArray();
        $request = new Request();
        $sonuc = $request->send("Logout", $param);
        if ($sonuc->REQUEST_RETURN->RETURN_CODE == "0") {
            return true;
        } else {
            $this->setHata($request->hataKod, $request->hataMesaj);
            return false;
        }
    }

    public function getUserList($okuma_zaman = "", $format = "XML")
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        if ($okuma_zaman != "") {
            $send_data["REGISTER_TIME_START"] = $okuma_zaman;
        }
        $send_data["FORMAT"] = $format;
        $req = new Request();
        $sonuc = $req->send("GetUserList", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->Items->Items;
    }

    public function getUserListBinary($type = "ZIP")
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $send_data["TYPE"] = $type;
        $req = new Request();
        $sonuc = $req->send("GetUserListBinary", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);

        if (isset($sonuc->Item)) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'edm_zip');
            file_put_contents($tmpFile, $sonuc->Item);

            $zip = new \ZipArchive();
            if ($zip->open($tmpFile) === TRUE) {
                $xmlContent = $zip->getFromIndex(0);
                $zip->close();
                unlink($tmpFile);

                // Parse XML
                $xml = @simplexml_load_string($xmlContent);
                if (!$xml)
                    return false;

                // Use xpath to find all Items/Items or similar structures
                $users = [];
                // GİB User List XML usually has a structure like <Items><Items>...
                // or just a list of elements.
                $items = $xml->xpath('//Items');
                if ($items) {
                    foreach ($items as $item) {
                        if (isset($item->IDENTIFIER)) { // This is an individual user node
                            $users[] = (object) [
                                'IDENTIFIER' => (string) $item->IDENTIFIER,
                                'TITLE' => (string) $item->TITLE,
                                'TYPE' => (string) $item->TYPE,
                                'UNIT' => (string) $item->UNIT
                            ];
                        }
                    }
                }

                // Fallback for different structures
                if (empty($users)) {
                    foreach ($xml->children() as $child) {
                        if (isset($child->IDENTIFIER)) {
                            $users[] = (object) [
                                'IDENTIFIER' => (string) $child->IDENTIFIER,
                                'TITLE' => (string) $child->TITLE,
                                'TYPE' => (string) $child->TYPE,
                                'UNIT' => (string) $child->UNIT
                            ];
                        }
                    }
                }

                return $users;
            }
        }
        return false;
    }

    public function checkUser($vkn = "", $alias = null, $unvan = null, $tip = null, $kayit_zaman = null)
    {
        if ($vkn == "") {
            return false;
        } else {
            $req_header = new RequestHeader();
            $req_header->session_id = $this->getSessionId();
            $send_data = $req_header->getArray();
            $send_data["USER"]["IDENTIFIER"] = $vkn;
            if (!is_null($alias)) {
                $send_data["USER"]["ALIAS"] = $alias;
            }
            if (!is_null($unvan)) {
                $send_data["USER"]["TITLE"] = $unvan;
            }
            if (!is_null($tip)) {
                $send_data["USER"]["TYPE"] = $tip;
            }
            if (!is_null($kayit_zaman)) {
                $send_data["USER"]["REGISTER_TIME"] = $kayit_zaman;
            }
            $req = new Request();
            $sonuc = $req->send("CheckUser", $send_data);
            $this->setHata($req->hataKod, $req->hataMesaj);
            return isset($sonuc->USER) ? $sonuc->USER : [];
        }
    }

    public function sendInvoice(Fatura $fatura)
    {

        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $readFatura = $fatura->readXML();
        $send_data["SENDER"] = array("_" => "", "alias" => $fatura->getDuzenleyen()->getGibUrn(), "vkn" => $fatura->getDuzenleyen()->getVkn());
        $send_data["RECEIVER"] = array("_" => "", "alias" => $fatura->getAlici()->getGibUrn(), "vkn" => $fatura->getAlici()->getVkn());
        $send_data["INVOICE"]["CONTENT"] = $readFatura;
        $req = new Request();
        $sonuc = $req->send("SendInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc;
    }

    public function loadInvoice(Fatura $fatura)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $readFatura = $fatura->readXML();
        $send_data["SENDER"] = array("_" => "", "alias" => $fatura->getDuzenleyen()->getGibUrn(), "vkn" => $fatura->getDuzenleyen()->getVkn());
        $send_data["RECEIVER"] = array("_" => "", "alias" => $fatura->getAlici()->getGibUrn(), "vkn" => $fatura->getAlici()->getVkn());
        $send_data["INVOICE"]["CONTENT"] = $readFatura;
        // Fix for SOAP-ERROR: Encoding: object has no 'GENERATEINVOICEIDONLOAD' property
        $send_data["GENERATEINVOICEIDONLOAD"] = "N";
        $req = new Request();
        $sonuc = $req->send("LoadInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc;
    }

    private function getInvoice($limit = 1, $alanURN = null, $gonderenURN = null, $faturaNo = null, $faturaUUID = null, $baslangicTarih = null, $bitisTarih = null, $gelenFaturalar = false, $contentType = "XML", $is_earchive = false, $is_archived = null, $is_draft = null)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = $limit;
        $send_data["INVOICE_CONTENT_TYPE"] = $contentType;
        if (!is_null($gonderenURN)) {
            $send_data["INVOICE_SEARCH_KEY"]["FROM"] = $gonderenURN;
        }
        if (!is_null($alanURN)) {
            $send_data["INVOICE_SEARCH_KEY"]["TO"] = $alanURN;
        }
        if (!is_null($faturaNo)) {
            $send_data["INVOICE_SEARCH_KEY"]["ID"] = $faturaNo;
        }
        if (!is_null($faturaUUID)) {
            $send_data["INVOICE_SEARCH_KEY"]["UUID"] = $faturaUUID;
        }
        if (!is_null($baslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["START_DATESpecified"] = true;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATESpecified"] = true;
        }
        if (!is_null($bitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["END_DATESpecified"] = true;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATESpecified"] = true;
        }
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDE"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDEDSpecified"] = true;

        if (!is_null($is_archived)) {
            $send_data["INVOICE_SEARCH_KEY"]["ISARCHIVED"] = $is_archived;
            $send_data["INVOICE_SEARCH_KEY"]["ISARCHIVEDSpecified"] = true;
        }

        if ($is_earchive) {
            $send_data["INVOICE_SEARCH_KEY"]["EARCHIVE"] = "Y";
            $send_data["INVOICE_SEARCH_KEY"]["EARCHIVESpecified"] = true;
        }

        if (!is_null($is_draft)) {
            $send_data["INVOICE_SEARCH_KEY"]["ISDRAFT"] = $is_draft;
            $send_data["INVOICE_SEARCH_KEY"]["ISDRAFTSpecified"] = true;
        }

        if ($gelenFaturalar) {
            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "IN";
        } else {
            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "OUT";
        }
        $req = new Request();
        $sonuc = $req->send("GetInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc;
    }

    public function UBLtoFatura($xmlStr = null)
    {
        if (is_null($xmlStr)) {
            return false;
        } else {
            $xmlStr = Util::UBLClear($xmlStr);
            //echo $xmlStr;
            $obj = simplexml_load_string($xmlStr);
            //Fatura Oluşturuluyor
            $fatura = new \Netesnaf\Edm\EFatura\Fatura();
            $fatura->setProfileId($obj->ProfileID);
            $fatura->setId($obj->ID);
            $fatura->setUuid($obj->UUID);
            $fatura->setIssueDate($obj->IssueDate);
            $fatura->setIssueTime($obj->IssueTime);
            $fatura->setInvoiceTypeCode($obj->InvoiceTypeCode);
            $fatura->setNote($obj->Note);
            $fatura->setDocumentCurrencyCode($obj->DocumentCurrencyCode);
            $fatura->setLineCountNumeric($obj->LineCountNumeric);
            if ($obj->AdditionalDocumentReference) {
                $fatura->setAdditionalDocumentReference(array(
                    "ID" => $obj->AdditionalDocumentReference->ID,
                    "IssueDate" => $obj->AdditionalDocumentReference->IssueDate,
                    "DocumentType" => $obj->AdditionalDocumentReference->DocumentType,
                    "Attachment" => array(
                        "EmbeddedDocumentBinaryObject" => $obj->AdditionalDocumentReference->Attachment->EmbeddedDocumentBinaryObject
                    )
                ));
            }

            //EFatura Gönderici Bilgileri Set Edildi.
            $objG = $obj->AccountingSupplierParty->Party;
            $duzenleyen = new \Netesnaf\Edm\EFatura\Cari();
            $duzenleyen->setUnvan($objG->PartyName->Name);
            $duzenleyen->setAdres($objG->PostalAddress->BuildingName);
            $duzenleyen->setIl($objG->PostalAddress->CityName);
            $duzenleyen->setIlce($objG->PostalAddress->CitySubdivisionName);
            $duzenleyen->setUlkeKod($objG->PostalAddress->Country->IdentificationCode);
            $duzenleyen->setUlkeAd($objG->PostalAddress->Country->Name);
            $duzenleyen->setVergiDaire($objG->PartyTaxScheme->TaxScheme->Name);
            $duzenleyen->setVkn($objG->PartyIdentification[0]["ID"]);
            $duzenleyen->setMersisno($objG->PartyIdentification[1]["ID"]);
            $duzenleyen->setHizmetno($objG->PartyIdentification[2]["ID"]);
            $duzenleyen->setTicaretSicilNo($objG->PartyIdentification[3]["ID"]);
            $duzenleyen->setTelefon($objG->Contact->Telephone);
            $duzenleyen->setEposta($objG->Contact->Telephone);
            $duzenleyen->setWebsite($objG->WebsiteURI);
            //$duzenleyen->setGibUrn($resp->INVOICE->HEADER->FROM);
            $fatura->setDuzenleyen($duzenleyen);
            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $objA = $obj->AccountingCustomerParty->Party;
            $alici = new \Netesnaf\Edm\EFatura\Cari();
            $alici->setUnvan($objG->PartyName->Name);
            $alici->setAdres($objG->PostalAddress->BuildingName);
            $alici->setIl($objG->PostalAddress->CityName);
            $alici->setIlce($objG->PostalAddress->CitySubdivisionName);
            $alici->setUlkeKod($objG->PostalAddress->Country->IdentificationCode);
            $alici->setUlkeAd($objG->PostalAddress->Country->Name);
            $alici->setVergiDaire($objG->PartyTaxScheme->TaxScheme->Name);
            $alici->setVkn($objG->PartyIdentification[0]["ID"]);
            $alici->setMersisno($objG->PartyIdentification[1]["ID"]);
            $alici->setHizmetno($objG->PartyIdentification[2]["ID"]);
            $alici->setTicaretSicilNo($objG->PartyIdentification[3]["ID"]);
            $alici->setTelefon($objG->Contact->Telephone);
            $alici->setEposta($objG->Contact->ElectronicMail);
            $alici->setWebsite($objG->WebsiteURI);
            //$alici->setGibUrn($resp->INVOICE->HEADER->FROM);
            $fatura->setAlici($alici);

            //Fatura Altı KDV Eklendi
            $fatura_dip_vergi = new \Netesnaf\Edm\EFatura\Vergi();
            $fatura_dip_vergi->setSiraNo(intval($obj->TaxTotal->TaxSubtotal->CalculationSequenceNumeric));
            $fatura_dip_vergi->setVergiHaricTutar($obj->TaxTotal->TaxSubtotal->TaxableAmount[0]);
            $fatura_dip_vergi->setVergiTutar($obj->TaxTotal->TaxAmount);
            $fatura_dip_vergi->setParaBirimKod($obj->TaxTotal->TaxAmount["currencyID"]);
            $fatura_dip_vergi->setVergiOran($obj->TaxTotal->TaxSubtotal->Percent);
            $fatura_dip_vergi->setVergiKod($obj->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->TaxTypeCode);
            $fatura_dip_vergi->setVergiAd($obj->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->Name);
            $fatura->setVergi($fatura_dip_vergi);


            //Faturaya Dip Toplamlar Ekleniyor

            $fatura->setSatirToplam($obj->LegalMonetaryTotal->LineExtensionAmount);
            $fatura->setVergiHaricToplam($obj->LegalMonetaryTotal->TaxExclusiveAmount);
            $fatura->setVergiDahilToplam($obj->LegalMonetaryTotal->TaxInclusiveAmount);
            $fatura->setToplamIskonto($obj->LegalMonetaryTotal->AllowanceTotalAmount);
            $fatura->setYuvarlamaTutar($obj->LegalMonetaryTotal->PayableRoundingAmount);
            $fatura->setOdenecekTutar($obj->LegalMonetaryTotal->PayableAmount);

            if (!is_null($obj->AllowanceCharge)) {
                $fIskonto = new \Netesnaf\Edm\EFatura\IskontoArttirim();
                $fIskonto->setTip($obj->AllowanceCharge->ChargeIndicator);
                $fIskonto->setOran($obj->AllowanceCharge->MultiplierFactorNumeric);
                $fIskonto->setTutar($obj->AllowanceCharge->Amount);
                $fIskonto->setParabirimKod($obj->AllowanceCharge->Amount["currencyID"]);
                $fIskonto->setUygTutar($obj->AllowanceCharge->BaseAmount);
                $fatura->setIskontoArttirim($fIskonto);
            }

            foreach ($obj->InvoiceLine as $line) {

                $satir = new \Netesnaf\Edm\EFatura\Satir();
                $satir->setSiraNo($line->ID);
                $satir->setBirim($line->InvoicedQuantity["unitCode"]);
                $satir->setMiktar($line->InvoicedQuantity);
                $satir->setBirimFiyat($line->Price->PriceAmount);
                $satir->setSatirToplam($line->LineExtensionAmount);
                $satir->setParaBirimKod($line->Price->PriceAmount["currencyID"]);

                $taxTotal = $line->TaxTotal;
                $satir_vergi = new \Netesnaf\Edm\EFatura\Vergi();
                if (is_array($taxTotal->TaxSubtotal)) {
                    foreach ($taxTotal->TaxSubtotal as $vergiDetay) {
                        $satir_vergi->setSiraNo(intval($vergiDetay->CalculationSequenceNumeric));
                        $satir_vergi->setVergiHaricTutar($vergiDetay->TaxableAmount);
                        $satir_vergi->setVergiTutar($taxTotal->TaxAmount);
                        $satir_vergi->setParaBirimKod($taxTotal->TaxAmount["currencyID"]);
                        $satir_vergi->setVergiOran($vergiDetay->Percent);
                        $satir_vergi->setVergiKod($vergiDetay->TaxCategory->TaxScheme->TaxTypeCode);
                        $satir_vergi->setVergiAd($vergiDetay->TaxCategory->TaxScheme->Name);
                    }
                } else {
                    $satir_vergi->setSiraNo(intval($taxTotal->TaxSubtotal->CalculationSequenceNumeric));
                    $satir_vergi->setVergiHaricTutar($taxTotal->TaxSubtotal->TaxableAmount);
                    $satir_vergi->setVergiTutar($taxTotal->TaxAmount);
                    $satir_vergi->setParaBirimKod($taxTotal->TaxAmount["currencyID"]);
                    $satir_vergi->setVergiOran($taxTotal->TaxSubtotal->Percent);
                    $satir_vergi->setVergiKod($taxTotal->TaxSubtotal->TaxCategory->TaxScheme->TaxTypeCode);
                    $satir_vergi->setVergiAd($taxTotal->TaxSubtotal->TaxCategory->TaxScheme->Name);
                }
                $satir->setVergi($satir_vergi);

                //İskonto Arttırım İşlemi
                if (!is_null($line->AllowanceCharge)) {
                    $iskontoSatir = new \Netesnaf\Edm\EFatura\IskontoArttirim();
                    $iskontoSatir->setTip($line->AllowanceCharge->ChargeIndicator);
                    $iskontoSatir->setOran($line->AllowanceCharge->MultiplierFactorNumeric);
                    $iskontoSatir->setTutar($line->AllowanceCharge->Amount);
                    $iskontoSatir->setParabirimKod($line->AllowanceCharge->Amount["currencyID"]);
                    $iskontoSatir->setUygTutar($line->AllowanceCharge->BaseAmount);
                    $satir->setIskontoArttirim($iskontoSatir);
                }

                $mal_hizmet = new \Netesnaf\Edm\EFatura\Urun();
                $mal_hizmet->setAd($line->Item->Name);
                $satir->setUrun($mal_hizmet);
                $fatura->addSatir($satir);

            }
            return $fatura;

        }
    }

    public function getSingleInvoice($faturaNo = null, $faturaUUID = null, $gelen = false, $contentType = "XML")
    {
        if (is_null($faturaNo) && is_null($faturaUUID)) {
            return false;
        } else {
            $req_header = new RequestHeader();
            $req_header->session_id = $this->getSessionId();
            $send_data = $req_header->getArray();

            $send_data["INVOICE_CONTENT_TYPE"] = $contentType;
            $send_data["HEADER_ONLY"] = "N";
            $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = 1;
            $send_data["INVOICE_SEARCH_KEY"]["LIMITSpecified"] = true;

            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = ($gelen ? "IN" : "OUT");
            $send_data["INVOICE_SEARCH_KEY"]["ID"] = $faturaNo;

            $req = new Request();
            $sonuc = $req->send("GetInvoice", $send_data);
            $this->setHata($req->hataKod, $req->hataMesaj);
            //print_r($sonuc);
            return array(
                "CONTENT" => $sonuc->INVOICE->CONTENT->_,
                "SENDER" => $sonuc->INVOICE->HEADER->SENDER,
                "RECEIVER" => $sonuc->INVOICE->HEADER->RECEIVER,
                "SUPPLIER" => $sonuc->INVOICE->HEADER->SUPPLIER,
                "CUSTOMER" => $sonuc->INVOICE->HEADER->CUSTOMER,
                "ISSUE_DATE" => $sonuc->INVOICE->HEADER->ISSUE_DATE,
                "PAYABLE_AMOUNT" => $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->_ . " " . $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->currencyID,
                "PARABIRIMI" => $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->currencyID,
                "FROM" => $sonuc->INVOICE->HEADER->FROM,
                "TO" => $sonuc->INVOICE->HEADER->TO,
                "PROFILEID" => $sonuc->INVOICE->HEADER->PROFILEID,

                "GIB_STATUS_CODE" => $sonuc->INVOICE->HEADER->GIB_STATUS_CODE,
                "GIB_STATUS_DESCRIPTION" => $sonuc->INVOICE->HEADER->GIB_STATUS_DESCRIPTION,
                "RESPONSE_CODE" => $sonuc->INVOICE->HEADER->RESPONSE_CODE,
                "RESPONSE_DESCRIPTION" => $sonuc->INVOICE->HEADER->RESPONSE_DESCRIPTION,
                "FILENAME" => $sonuc->INVOICE->HEADER->FILENAME,
                "HASH" => $sonuc->INVOICE->HEADER->HASH,
                "CDATE" => $sonuc->INVOICE->HEADER->CDATE,
                "ENVELOPE_IDENTIFIER" => $sonuc->INVOICE->HEADER->ENVELOPE_IDENTIFIER,
                "INTERNETSALES" => $sonuc->INVOICE->HEADER->INTERNETSALES,
                "EARCHIVE" => $sonuc->INVOICE->HEADER->EARCHIVE,
                "TRXID" => $sonuc->INVOICE->TRXID,
                "UUID" => $sonuc->INVOICE->UUID,
                "ID" => $sonuc->INVOICE->ID
            );
        }
    }

    public function getInvoiceStatus($faturaNo = "", $faturaUUID = null)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["INVOICE"] = array("_" => "", "ID" => $faturaNo, "UUID" => $faturaUUID);
        $req = new Request();
        $sonuc = $req->send("GetInvoiceStatus", $send_data);
        $sonuc->INVOICE_STATUS->ACIKLAMA = Util::invoiceStatus($sonuc->INVOICE_STATUS->STATUS);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->INVOICE_STATUS;
    }

    public function getIncomingInvoice($limit = 10, $vkn = null, $pk = null, $baslangicTarih = null, $bitisTarih = null, $crbaslangicTarih = null, $crbitisTarih = null)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();

        $send_data["INVOICE_CONTENT_TYPE"] = "XML";
        $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = $limit;
        $send_data["INVOICE_SEARCH_KEY"]["LIMITSpecified"] = true;

        $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "IN";
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDED"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDEDSpecified"] = false;

        if (!is_null($vkn)) {
            $send_data["INVOICE_SEARCH_KEY"]["RECEIVER"] = $vkn;
        }
        if (!is_null($pk)) {
            $send_data["INVOICE_SEARCH_KEY"]["TO"] = $pk;
        }
        if (!is_null($baslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["START_DATESpecified"] = true;
        }
        if (!is_null($bitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["END_DATESpecified"] = true;
        }

        if (!is_null($crbaslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATE"] = $crbaslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATESpecified"] = true;
        }
        if (!is_null($crbitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATE"] = $crbitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATESpecified"] = true;
        }
        $req = new Request();
        $sonuc = $req->send("GetInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);

        $cevap = array();
        if (isset($sonuc->INVOICE)) {
            $invoices = is_array($sonuc->INVOICE) ? $sonuc->INVOICE : [$sonuc->INVOICE];
            foreach ($invoices as $key => $fatura) {
                $cevap[$key] = array(
                    "SENDER" => $fatura->HEADER->SENDER,
                    "RECEIVER" => $fatura->HEADER->RECEIVER,
                    "SUPPLIER" => $fatura->HEADER->SUPPLIER,
                    "CUSTOMER" => $fatura->HEADER->CUSTOMER,
                    "ISSUE_DATE" => $fatura->HEADER->ISSUE_DATE,
                    "PAYABLE_AMOUNT" => $fatura->HEADER->PAYABLE_AMOUNT->_ . " " . $fatura->HEADER->PAYABLE_AMOUNT->currencyID,
                    "FROM" => $fatura->HEADER->FROM,
                    "TO" => $fatura->HEADER->TO,
                    "PROFILEID" => $fatura->HEADER->PROFILEID,
                    "STATUS" => $fatura->HEADER->STATUS,
                    "STATUS_DESCRIPTION" => $fatura->HEADER->STATUS_DESCRIPTION,
                    "ACIKLAMA" => Util::invoiceStatus($fatura->HEADER->STATUS),
                    "GIB_STATUS_CODE" => $fatura->HEADER->GIB_STATUS_CODE,
                    "GIB_STATUS_DESCRIPTION" => $fatura->HEADER->GIB_STATUS_DESCRIPTION,
                    "RESPONSE_CODE" => $fatura->HEADER->RESPONSE_CODE,
                    "RESPONSE_DESCRIPTION" => $fatura->HEADER->RESPONSE_DESCRIPTION,
                    "FILENAME" => $fatura->HEADER->FILENAME,
                    "HASH" => $fatura->HEADER->HASH,
                    "CDATE" => isset($fatura->HEADER->CDATE) ? new \DateTime($fatura->HEADER->CDATE) : null,
                    "ENVELOPE_IDENTIFIER" => $fatura->HEADER->ENVELOPE_IDENTIFIER ?? null,
                    "INTERNETSALES" => $fatura->HEADER->INTERNETSALES ?? null,
                    "EARCHIVE" => $fatura->HEADER->EARCHIVE ?? null,
                    "TRXID" => $fatura->TRXID ?? null,
                    "UUID" => $fatura->UUID ?? null,
                    "ID" => $fatura->ID ?? null,
                    "TYPE" => $fatura->HEADER->INVOICE_TYPE ?? null,
                    "SENDTYPE" => $fatura->HEADER->INVOICE_SEND_TYPE ?? null
                );
            }
        }
        return $cevap;
    }

    public function getOutgoingInvoice($limit = 100, $baslangic = null, $bitis = null, $is_earchive = false, $is_archived = null, $is_draft = null)
    {
        $sonuc = $this->getInvoice($limit, null, null, null, null, $baslangic, $bitis, false, "XML", $is_earchive, $is_archived, $is_draft);
        $cevap = array();
        if (isset($sonuc->INVOICE)) {
            $invoices = is_array($sonuc->INVOICE) ? $sonuc->INVOICE : [$sonuc->INVOICE];
            foreach ($invoices as $key => $fatura) {
                $cevap[$key] = array(
                    "SENDER" => $fatura->HEADER->SENDER,
                    "RECEIVER" => $fatura->HEADER->RECEIVER,
                    "SUPPLIER" => $fatura->HEADER->SUPPLIER,
                    "CUSTOMER" => $fatura->HEADER->CUSTOMER,
                    "ISSUE_DATE" => $fatura->HEADER->ISSUE_DATE,
                    "PAYABLE_AMOUNT" => $fatura->HEADER->PAYABLE_AMOUNT->_ . " " . $fatura->HEADER->PAYABLE_AMOUNT->currencyID,
                    "FROM" => $fatura->HEADER->FROM,
                    "TO" => $fatura->HEADER->TO,
                    "PROFILEID" => $fatura->HEADER->PROFILEID,
                    "STATUS" => $fatura->HEADER->STATUS,
                    "STATUS_DESCRIPTION" => $fatura->HEADER->STATUS_DESCRIPTION,
                    "ACIKLAMA" => Util::invoiceStatus($fatura->HEADER->STATUS),
                    "GIB_STATUS_CODE" => $fatura->HEADER->GIB_STATUS_CODE,
                    "GIB_STATUS_DESCRIPTION" => $fatura->HEADER->GIB_STATUS_DESCRIPTION,
                    "RESPONSE_CODE" => $fatura->HEADER->RESPONSE_CODE,
                    "RESPONSE_DESCRIPTION" => $fatura->HEADER->RESPONSE_DESCRIPTION,
                    "FILENAME" => $fatura->HEADER->FILENAME,
                    "HASH" => $fatura->HEADER->HASH ?? null,
                    "CDATE" => $fatura->HEADER->CDATE ?? null,
                    "ENVELOPE_IDENTIFIER" => $fatura->HEADER->ENVELOPE_IDENTIFIER ?? null,
                    "INTERNETSALES" => $fatura->HEADER->INTERNETSALES ?? null,
                    "EARCHIVE" => $fatura->HEADER->EARCHIVE ?? null,
                    "TRXID" => $fatura->TRXID ?? null,
                    "UUID" => $fatura->UUID ?? null,
                    "ID" => $fatura->ID ?? null,
                    "INVOICE_TYPE" => $fatura->HEADER->INVOICE_TYPE ?? null
                );
            }
        }
        return $cevap;
    }

    public function markInvoice($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["MARK"]["INVOICE"] = array("_" => "", "ID" => $faturaID);
        $req = new Request();
        $sonuc = $req->send("MarkInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->REQUEST_RETURN;
    }

    public function gelenFaturaKabul($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["STATUS"] = "KABUL";
        $send_data["INVOICE"]["ID"] = $faturaID;
        $req = new Request();
        $sonuc = $req->send("SendInvoiceResponseWithServerSign", $send_data);
        return $sonuc->REQUEST_RETURN;
    }

    public function gelenFaturaRed($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["STATUS"] = "RED";
        $send_data["INVOICE"]["ID"] = $faturaID;
        $req = new Request();
        $sonuc = $req->send("SendInvoiceResponseWithServerSign", $send_data);
        return $sonuc->REQUEST_RETURN;
    }

    public function getCompanyDetails($vkn)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();

        $send_data["USER_NAME"] = $this->username;
        $send_data["PASSWORD"] = $this->password;
        $send_data["TAXNUMBER"] = $vkn;
        $send_data["SETCOMPANYMM"] = false;
        $send_data["DELETECOMPANYMM"] = false;
        $send_data["KEY"] = "";

        $req = new Request();
        $sonuc = $req->send("GetCompany", $send_data);

        $comp = null;
        if (isset($sonuc->GetCompanyList->GetCompanyResponseList)) {
            $list = $sonuc->GetCompanyList->GetCompanyResponseList;
            if (is_array($list) && count($list) > 0) {
                $comp = $list[0];
            } else if (is_object($list)) {
                $comp = $list;
            }
        } elseif (isset($sonuc->GetCompanyList) && is_object($sonuc->GetCompanyList)) {
            $comp = $sonuc->GetCompanyList;
        }

        if ($comp) {
            // Map UNVAN to TITLE for consistency if needed
            if (isset($comp->UNVAN) && !isset($comp->TITLE)) {
                $comp->TITLE = $comp->UNVAN;
            }
        }

        return $comp;
    }

    // Standalone Full Sync Helper
    public function fullSyncUsers()
    {
        // drastically increase limits for this operation
        @ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();

        // Request FULL list (no date filter)
        $send_data = $req_header->getArray();
        $send_data["FORMAT"] = "XML";

        $req = new Request();
        // Note: This returns a huge SoapResponse object
        $sonuc = $req->send("GetUserList", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);

        if (isset($sonuc->Items->Items)) {
            $list = $sonuc->Items->Items;
            // Merge with address book immediately
            $list = $this->mergeAddressBook($list, __DIR__ . '/address_book.json');

            // Save to cache
            $cacheFile = '/tmp/edm_user_list_cache_' . md5(Util::$service_url) . '.json';
            file_put_contents($cacheFile, json_encode($list));

            return count($list);
        }
        return false;
    }

    public function getRecipientDetails($vkn)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $send_data["VKN"] = $vkn;
        $req = new Request();
        try {
            $sonuc = $req->send("GetTurmob", $send_data);
            if (isset($sonuc->MUKELLEF) && $sonuc->MUKELLEF->durum->sonuc) {
                return $sonuc->MUKELLEF;
            }
        } catch (\Exception $e) {
            // Silently fail or log if needed
        }
        return null;
    }

    public function getUserListCached($force = false)
    {
        $cacheFile = '/tmp/edm_user_list_cache_' . md5(Util::$service_url) . '.json';
        $cacheTTL = 24 * 60 * 60; // 24 hours

        $cachedData = null;
        if (!$force && file_exists($cacheFile)) {
            $mtime = filemtime($cacheFile);
            if (time() - $mtime < $cacheTTL) {
                // FORCE MERGE even on valid cache
                $data = json_decode(file_get_contents($cacheFile));
                $addressBookPath = __DIR__ . '/address_book.json';
                return $this->mergeAddressBook($data ?: [], $addressBookPath);
            }
            $cachedData = json_decode(file_get_contents($cacheFile));
        }

        // Increase memory for large list processing
        @ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        // Only try to fetch if we have a session
        $sid = $this->getSessionId();
        if (!$sid) {
            return $cachedData ?: [];
        }

        try {
            $newList = $this->getUserListBinary();
        } catch (\Exception $e) {
            $newList = false;
        }

        if (!$newList) {
            try {
                // Try standard full sync if binary fails
                $newList = $this->getUserList();
            } catch (\Exception $e) {
                $newList = false;
            }
        }

        // Fallback: If full sync fails, try incremental (last 10 days)
        if (!$newList) {
            try {
                $tenDaysAgo = date("Y-m-d", strtotime("-10 days")) . "T00:00:00";
                $newList = $this->getUserList($tenDaysAgo);
            } catch (\Exception $e) {
                $newList = false;
            }
        }

        if ($newList && is_array($newList)) {
            $json = json_encode($newList);
            if ($json !== false) {
                @file_put_contents($cacheFile, $json);
            }
            // CRITICAL FIX: Merge Address Book even after successful sync!
            $addressBookPath = __DIR__ . '/address_book.json';
            return $this->mergeAddressBook($newList, $addressBookPath);
        }

        // Merge Address Book (Runtime) - EVEN IF CACHE IS EMPTY
        $addressBookPath = __DIR__ . '/address_book.json';
        $finalList = is_array($cachedData) ? $cachedData : [];
        return $this->mergeAddressBook($finalList, $addressBookPath);
    }

    private function mergeAddressBook($list, $path)
    {
        if (!file_exists($path)) {
            return $list;
        }
        $ab = json_decode(file_get_contents($path));
        if ($ab && is_array($ab)) {
            return array_merge($list, $ab);
        }
        return $list;
    }

    public function checkUserByName($query)
    {
        $all = $this->getUserListCached();
        if (!$all) {
            return [];
        }

        $results = [];
        // Universal Turkish-safe uppercase conversion
        $queryUpper = mb_convert_case(str_replace(['i', 'ı'], ['İ', 'I'], $query), MB_CASE_UPPER, "UTF-8");

        foreach ($all as $u) {
            if (isset($u->TITLE)) { // Uses object PROPERTY access
                $titleUpper = mb_convert_case(str_replace(['i', 'ı'], ['İ', 'I'], (string) $u->TITLE), MB_CASE_UPPER, "UTF-8");
                if (mb_strpos($titleUpper, $queryUpper) !== false) {
                    $results[] = $u;
                }
            }
        }

        // Return first 50 results to avoid UI bloat
        return array_slice($results, 0, 50);
    }

    public function getInvoicePDF($uuid)
    {
        // Increase timeout for PDF download
        set_time_limit(120);

        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();

        // Search by UUID
        $send_data["INVOICE_SEARCH_KEY"]["UUID"] = $uuid;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDE"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDEDSpecified"] = true;
        $send_data["INVOICE_CONTENT_TYPE"] = "PDF";

        // Check both directions just in case, or default to OUT
        $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "OUT";

        $req = new Request();
        try {
            $sonuc = $req->send("GetInvoice", $send_data);

            if (isset($sonuc->INVOICE)) {
                // If multiple, pick first (should be unique by UUID)
                $inv = is_array($sonuc->INVOICE) ? $sonuc->INVOICE[0] : $sonuc->INVOICE;

                if (isset($inv->CONTENT)) {
                    // Check if content is object or string
                    if (is_object($inv->CONTENT) && isset($inv->CONTENT->_)) {
                        return $inv->CONTENT->_;
                    }
                    return $inv->CONTENT;
                }
            }
        } catch (\Exception $e) {
            $this->setHata("PDF_ERR", $e->getMessage());
        }
        return false;
    }
}