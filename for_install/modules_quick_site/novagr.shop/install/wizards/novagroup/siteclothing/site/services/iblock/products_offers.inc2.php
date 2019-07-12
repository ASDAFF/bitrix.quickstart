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

    $permissions = Array(
        "1" => "X",
        "2" => "R"
    );

    $iblockID1 = WizardServices::ImportIBlockFromXML(
        $iblockXMLFilePrices,
        $iblockXML,
        $iblockType,
        WIZARD_SITE_ID,
        $permissions
    );

}

?>