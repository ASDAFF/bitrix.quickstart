<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock"))
    return;

if (COption::GetOptionString("innet", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
    return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH . "/xml/" . LANGUAGE_ID . "/forms/catalog_orders.xml";
$iblockCode = "catalog_orders_" . WIZARD_SITE_ID;
$iblockType = "innet_forms_" . WIZARD_SITE_ID;

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;

if ($arIBlock = $rsIBlock->Fetch()) {
    $iblockID = $arIBlock["ID"];
    if (WIZARD_REINSTALL_DATA) {
        CIBlock::Delete($arIBlock["ID"]);
        $iblockID = false;
    }
}

if ($iblockID == false) {

    $permissions = Array("1" => "X", "2" => "R");

    $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
    if ($arGroup = $dbGroup->Fetch()) {
        $permissions[$arGroup["ID"]] = 'W';
    }

    $iblockID = WizardServices::ImportIBlockFromXML($iblockXMLFile, $iblockCode, $iblockType, WIZARD_SITE_ID, $permissions);

    if ($iblockID < 1)
        return;

    $iblock = new CIBlock;
    $arFields = Array(
        "ACTIVE" => "Y",
        "FIELDS" => array('IBLOCK_SECTION' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'ACTIVE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y',), 'ACTIVE_FROM' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today',), 'ACTIVE_TO' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'SORT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'NAME' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '',), 'PREVIEW_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N',),), 'PREVIEW_TEXT_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text',), 'PREVIEW_TEXT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'DETAIL_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95,),), 'DETAIL_TEXT_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text',), 'DETAIL_TEXT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'XML_ID' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'CODE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array('UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y',),), 'TAGS' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'SECTION_NAME' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '',), 'SECTION_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N',),), 'SECTION_DESCRIPTION_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text',), 'SECTION_DESCRIPTION' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'SECTION_DETAIL_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95,),), 'SECTION_XML_ID' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',), 'SECTION_CODE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N',),),),
        "CODE" => $iblockCode,
        "XML_ID" => $iblockCode,
    );

    $iblock->Update($iblockID, $arFields);
} else {
    $arSites = array();
    $db_res = CIBlock::GetSite($iblockID);
    while ($res = $db_res->Fetch())
        $arSites[] = $res["LID"];
    if (!in_array(WIZARD_SITE_ID, $arSites)) {
        $arSites[] = WIZARD_SITE_ID;
        $iblock = new CIBlock;
        $iblock->Update($iblockID, array("LID" => $arSites));
    }
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "catalog/index.php", array('INNET_IBLOCK_ID_CATALOG_ORDERS' => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "_index.php", array('INNET_IBLOCK_ID_CATALOG_ORDERS' => $iblockID));

?>