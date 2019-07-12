<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

   
//$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/ru/".WIZARD_TEMPLATE_ID."/actions.xml"; 
$iblockXMLFile = WIZARD_ABSOLUTE_PATH."/site/services/iblock/xml/ru/".WIZARD_TEMPLATE_ID."/actions.xml"; 

//$iblockCode = "actions_".WIZARD_SITE_ID; 
$iblockCode = "company_actions"; 
$iblockCodeOriginal = "company_actions"; 
$iblockType = "actions"; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
    $iblockID = $arIBlock["ID"]; 
    if (WIZARD_INSTALL_DEMO_DATA)
    {
        CIBlock::Delete($arIBlock["ID"]); 
        $iblockID = false; 
    }
}

if($iblockID == false)
{
    $permissions = Array(
            "1" => "X",
            "2" => "R"
        );
    $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
    if($arGroup = $dbGroup -> Fetch())
    {
        $permissions[$arGroup["ID"]] = 'W';
    };
    

    $iblockID = WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockCodeOriginal,
        $iblockType,
        WIZARD_SITE_ID//,
        //$permissions
    );

    /*$iblockID = WizardServices::ImportIBlockFromXML(
        $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/iblock/iblocks.xml", 
        "credit_elem", 
        "credit_elem", 
        WIZARD_SITE_ID
    );*/

    if ($iblockID < 1) {
        print 'Error on create iblock "actions"';
        return;
    }


	//IBlock fields
	/*$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'Y', 'SCALE' => 'Y', 'WIDTH' => 90, 'HEIGHT' => '', 'IGNORE_ERRORS' => 'Y', 'METHOD' => 'resample', 'COMPRESSION' => 85), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'Y', 'WIDTH' => 248, 'HEIGHT' => '', 'IGNORE_ERRORS' => 'Y', 'METHOD' => 'resample', 'COMPRESSION' => 85 ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		"NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
	);
	
	$iblock->Update($iblockID, $arFields);   */
}
else
{
    $arSites = array(); 
    $db_res = CIBlock::GetSite($iblockID);
    while ($res = $db_res->Fetch())
        $arSites[] = $res["LID"]; 
    if (!in_array(WIZARD_SITE_ID, $arSites))
    {
        $arSites[] = WIZARD_SITE_ID;
        $iblock = new CIBlock;
        $iblock->Update($iblockID, array("LID" => $arSites));
    }
}


//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("NEWS_IBLOCK_ID" => $iblockID));
//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index_news.php", array("NEWS_IBLOCK_ID" => $iblockID));
//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/news/index.php", array("NEWS_IBLOCK_ID" => $iblockID));

?>
