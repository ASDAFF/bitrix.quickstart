<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock"))
    return;

if (!CModule::IncludeModule("catalog"))
    return;

if (COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
    return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH . "/xml/" . LANGUAGE_ID . "/" . COption::GetOptionString("novagr.shop", 'xml_products_offers_file' . WIZARD_SITE_ID) . ".xml";
$iblockXMLFilePrices = WIZARD_SERVICE_RELATIVE_PATH . "/xml/" . LANGUAGE_ID . "/" . COption::GetOptionString("novagr.shop", 'xml_products_offers_file' . WIZARD_SITE_ID) . "_prices.xml";
$iblockXML = COption::GetOptionInt("novagr.shop", 'xml_products_offers' . WIZARD_SITE_ID);
$iblockCode = COption::GetOptionString("novagr.shop", 'xml_products_offers_code' . WIZARD_SITE_ID);
$iblockType = "offers";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXML, "TYPE" => $iblockType));

if ($arIBlock = $rsIBlock->Fetch()) {

    $offiblockID = $iblockID = $arIBlock["ID"];
    $iblockCodeFur = COption::GetOptionString("novagr.shop", 'xml_products_file'.WIZARD_SITE_ID);
    $iblockTypeFur = "catalog";
    $iblockTypeFurXML = COption::GetOptionInt("novagr.shop", 'xml_products' . WIZARD_SITE_ID);

    $rsIBlockFur = CIBlock::GetList(array(), array("XML_ID" => $iblockTypeFurXML, "TYPE" => $iblockTypeFur));

    if ($arIBlockFur = $rsIBlockFur->Fetch()) {
        $ID_SKU = CCatalog::LinkSKUIBlock($arIBlockFur["ID"], $offiblockID);
    }

    $arCatalog = CCatalog::GetByID($offiblockID);
    if ($arCatalog) {
        CCatalog::Update($offiblockID, array('PRODUCT_IBLOCK_ID' => $arIBlockFur["ID"], 'SKU_PROPERTY_ID' => $ID_SKU));
    } else {
        CCatalog::Add(array('IBLOCK_ID' => $offiblockID, 'PRODUCT_IBLOCK_ID' => $arIBlockFur["ID"], 'SKU_PROPERTY_ID' => $ID_SKU));
    }

}

?>