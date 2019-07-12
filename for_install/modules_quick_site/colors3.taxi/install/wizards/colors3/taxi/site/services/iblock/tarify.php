<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(COption::GetOptionString("taxi", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
	if($wizard->GetVar('rewriteIndex', true) && $wizard->GetVar('siteLogoSet', true)){
		$iblockCode = "tarify".WIZARD_SITE_ID; 
		$iblockType = "taxis";
		
		$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
		$iblockID = false; 
		if ($arIBlock = $rsIBlock->Fetch())
		{
			CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/prices/index.php", array("TARIFY_IBLOCK_ID" => $iblockID));
			CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/prices/detail.php", array("TARIFY_IBLOCK_ID" => $iblockID));
		}
	}
	return;
}

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/tarify.xml"; 
$iblockCode = "tarify".WIZARD_SITE_ID; 
$iblockType = "taxis"; 

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
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
		"tarify",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($iblockID < 1)
		return;
	
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 
		'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 
		'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 
		'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => $iblock->GetArrayByID($iblockID, "NAME"), ), 
		'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'Y', 'DELETE_WITH_DETAIL' =>'Y', 'UPDATE_WITH_DETAIL'=>'Y', 'SCALE' => 'Y', 'WIDTH' => '261', 'HEIGHT' => '196', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => '100'), ), 
		'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'Y', 'WIDTH' => '800', 'HEIGHT' => '800', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => '100'), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
		'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '-', 'TRANS_OTHER' => '-', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), 
		'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
		'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 
		'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 
		'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 
		'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
		"CODE" => "tarify", 
		"XML_ID" => $iblockCode,
		//"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
	);
	
	$iblock->Update($iblockID, $arFields);
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

$arProperty = Array();
$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $iblockID));
while($arProp = $dbProperty->Fetch())
	$arProperty[$arProp["CODE"]] = $arProp["ID"];

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";
	
WizardServices::IncludeServiceLang("tarify.php", $lang);

CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_TARIFF_1")
		.'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_TARIFF_2")
		.'--,--NAME--#--'.GetMessage("WZD_OPTION_TARIFF_3")
		.'--,--CODE--#--'.GetMessage("WZD_OPTION_TARIFF_4")
		.'--,--PROPERTY_'.$arProperty["PRICE_KM_CITY"].'--#--'.GetMessage("WZD_OPTION_TARIFF_5")
		.'--,--PROPERTY_'.$arProperty["PRICE_KM_TR"].'--#--'.GetMessage("WZD_OPTION_TARIFF_6")
		.'--,--PROPERTY_'.$arProperty["PRICE_MIN_CITY"].'--#--'.GetMessage("WZD_OPTION_TARIFF_7")
		.'--,--PROPERTY_'.$arProperty["KM_VKL_POSAD"].'--#--'.GetMessage("WZD_OPTION_TARIFF_20")
		.'--,--PROPERTY_'.$arProperty["PRICE_MIN_TR"].'--#--'.GetMessage("WZD_OPTION_TARIFF_8")
		.'--,--PROPERTY_'.$arProperty["PRICE_POSADKA_CITY"].'--#--'.GetMessage("WZD_OPTION_TARIFF_9")
		.'--,--PROPERTY_'.$arProperty["PRICE_POSADKA_TR"].'--#--'.GetMessage("WZD_OPTION_TARIFF_10")
		.'--,--PROPERTY_'.$arProperty["WAIT_MIN_FREE"].'--#--'.GetMessage("WZD_OPTION_TARIFF_11")
		.'--,--PROPERTY_'.$arProperty["WAIT_MIN_PRICE"].'--#--'.GetMessage("WZD_OPTION_TARIFF_12")
		.'--,--PROPERTY_'.$arProperty["MIN_PRICE_CITY"].'--#--'.GetMessage("WZD_OPTION_TARIFF_13")
		.'--,--PROPERTY_'.$arProperty["MIN_PRICE_TR"].'--#--'.GetMessage("WZD_OPTION_TARIFF_14")
		.'--,--PROPERTY_'.$arProperty["PRED_PRICE"].'--#--'.GetMessage("WZD_OPTION_TARIFF_15")
		.'--,--PROPERTY_'.$arProperty["TIME_PRICE"].'--#--'.GetMessage("WZD_OPTION_TARIFF_16")
		.'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_TARIFF_17")
		.'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_TARIFF_19")
		.'--,--SORT--#--'.GetMessage("WZD_OPTION_TARIFF_18").'--;--', ));
CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'ACTIVE,PROPERTY_'.$arProperty["FIO"].',SORT', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/prices/index.php", array("TARIFY_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/prices/detail.php", array("TARIFY_IBLOCK_ID" => $iblockID));

?>