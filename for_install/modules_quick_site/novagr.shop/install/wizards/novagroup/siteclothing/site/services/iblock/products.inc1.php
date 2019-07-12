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
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch()) {

    $iblockID = $arIBlock["ID"];

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
        "NAME" => "production",
        "CODE" => $iblockCode,
        "EXTERNAL_ID" => $iblockXML,
        "IBLOCK_TYPE_ID" => $iblockType,
        "SITE_ID" => array(WIZARD_SITE_ID),
        "VERSION" => 2,
        'GROUP_ID' => $permissions
    );
    $iblockID = $ib->Add($arF);

    if ($iblockID > 0) {
        $arFields = Array
        (
            'CODE' => array(
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => Array
                (
                    'UNIQUE' => 'Y',
                    'TRANSLITERATION' => 'Y',
                    'TRANS_LEN' => '100',
                    'TRANS_CASE' => 'L',
                    'TRANS_SPACE' => '-',
                    'TRANS_OTHER' => '-',
                    'TRANS_EAT' => 'Y',
                    'USE_GOOGLE' => 'N'
                )
            ),
            'SECTION_CODE' => array(
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => Array
                (
                    'UNIQUE' => 'Y',
                    'TRANSLITERATION' => 'Y',
                    'TRANS_LEN' => '100',
                    'TRANS_CASE' => 'L',
                    'TRANS_SPACE' => '-',
                    'TRANS_OTHER' => '-',
                    'TRANS_EAT' => 'Y',
                    'USE_GOOGLE' => 'N'
                )
            ),
        );
        CIBlock::SetFields($iblockID, $arFields);
    }


    WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockXML,
        $iblockType,
        WIZARD_SITE_ID,
        $permissions
    );


}
?>