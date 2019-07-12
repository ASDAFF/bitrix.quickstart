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
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch()) {

    //set params
    $iblockID = $arIBlock["ID"];
    $arFields = Array
    (
        'IBLOCK_SECTION' => array(
            'IS_REQUIRED' => 'N',
        ),
    );

    //set fields
    CIBlock::SetFields($iblockID, $arFields);

    //set sites
    $arSites = array();
    $db_res = CIBlock::GetSite($iblockID);
    while ($res = $db_res->Fetch())
        $arSites[] = $res["LID"];
    if (!in_array(WIZARD_SITE_ID, $arSites)) {
        $arSites[] = WIZARD_SITE_ID;
        $iblock = new CIBlock;
        $iblock->Update($iblockID, array("LID" => $arSites));
    }
} else {
    $permissions = Array(
        "1" => "X",
        "2" => "R"
    );

    $ib = new CIBlock;
    $arF = array(
        "NAME" => "offers",
        "CODE" => $iblockCode,
        "EXTERNAL_ID" => $iblockXML,
        "IBLOCK_TYPE_ID" => $iblockType,
        "SITE_ID" => array(WIZARD_SITE_ID),
        "VERSION" => 2,
        'GROUP_ID' => $permissions
    );
    $iblockID = $ib->Add($arF);

    $iblockID = WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockXML,
        $iblockType,
        WIZARD_SITE_ID,
        $permissions
    );


}

?>