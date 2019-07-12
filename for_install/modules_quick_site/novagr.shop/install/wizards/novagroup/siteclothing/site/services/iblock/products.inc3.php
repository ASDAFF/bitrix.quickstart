<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock"))
    return;

if (!CModule::IncludeModule("catalog"))
    return;

if (COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
    return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH . "/xml/" . LANGUAGE_ID . "/" . COption::GetOptionString("novagr.shop", 'xml_products_file' . WIZARD_SITE_ID) . ".xml";
$iblockXMLFilePrices = WIZARD_SERVICE_RELATIVE_PATH . "/xml/" . LANGUAGE_ID . "/" . COption::GetOptionString("novagr.shop", 'xml_products_file' . WIZARD_SITE_ID) . "_prices.xml";
$iblockXML = COption::GetOptionInt("novagr.shop", 'xml_products' . WIZARD_SITE_ID);
$iblockCode = COption::GetOptionString("novagr.shop", 'xml_products_code' . WIZARD_SITE_ID);
$iblockType = "catalog";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXML, "TYPE" => $iblockType));

if ($arIBlock = $rsIBlock->Fetch()) {

    $iblockID = $arIBlock["ID"];

    try {

        $xml = simplexml_load_file(getenv('DOCUMENT_ROOT') . $iblockXMLFile);

        $Klassifikator = iconv('CP1251', 'UTF-8', 'Классификатор');
        $Svoystva = iconv('CP1251', 'UTF-8', 'Свойства');
        $BitrixCode = iconv('CP1251', 'UTF-8', 'БитриксКод');
        $ID = iconv('CP1251', 'UTF-8', 'Ид');

        $RECOMMEND = 0;

        if (isset($xml) && isset($xml->$Klassifikator) && isset($xml->$Klassifikator->$Svoystva)) {
            foreach ($xml->$Klassifikator->$Svoystva as $key => $data) {
                foreach ($data as $Svoystva => $properties) {
                    if (isset($properties->{$BitrixCode}) and $properties->{$BitrixCode} == "RECOMMEND") {
                        $RECOMMEND = $properties->$ID;
                    }
                }
            }
        }

        if ($RECOMMEND > 0 and $iblockID > 0) {
            $Katalog = iconv('CP1251', 'UTF-8', 'Каталог');
            $Tovari = iconv('CP1251', 'UTF-8', 'Товары');
            $Zna4eniaSvoystv = iconv('CP1251', 'UTF-8', 'ЗначенияСвойств');
            $Zna4enie = iconv('CP1251', 'UTF-8', 'Значение');
            $Zna4eniya = array();

            if (isset($xml) && isset($xml->$Katalog) && isset($xml->$Katalog->$Tovari)) {
                foreach ($xml->$Katalog->$Tovari as $key => $data) {
                    foreach ($data as $properties) {
                        $itemID = intval($properties->$ID);
                        if (isset($properties->$Zna4eniaSvoystv)) {
                            foreach ($properties->$Zna4eniaSvoystv as $item) {
                                foreach ($item as $qwqw) {
                                    if ($qwqw->$ID == (int)$RECOMMEND) {
                                        $Zna4eniya[$itemID] = (array)$qwqw->$Zna4enie;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($Zna4eniya as $KEY => $Zna4enie) {
                if (is_array($Zna4enie) and count($Zna4enie) > 0) {
                    $XMLKI = array();
                    $XML_ID = 0;
                    $arSelect = Array("ID", "NAME", "XML_ID");
                    $XML_ID_ARRAY = $Zna4enie;
                    $XML_ID_ARRAY[] = $KEY;
                    $arFilter = Array("IBLOCK_ID" => IntVal($iblockID), "XML_ID" => $XML_ID_ARRAY);
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                    while ($ob = $res->Fetch()) {
                        if (in_array($ob['XML_ID'], $Zna4enie)) {
                            $XMLKI[] = $ob['ID'];
                        }
                        if (in_array($ob['XML_ID'], array($KEY))) {
                            $XML_ID = $ob['ID'];
                        }
                    }

                    if (is_array($XMLKI) && count($XMLKI) > 0 and $XML_ID > 0) {
                        CIBlockElement::SetPropertyValueCode($XML_ID, "RECOMMEND", $XMLKI);
                    }
                }
            }
        }

    } catch (Exception $e) {
        /* Exception :: Ошибка при прикреплении рекомендуемых товаров */
    }
}

?>