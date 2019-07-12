<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(COption::GetOptionString("taxi", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
	if($wizard->GetVar('rewriteIndex', true) && $wizard->GetVar('siteLogoSet', true)){
		$iblockCode = "orders".WIZARD_SITE_ID; 
		$iblockType = "orders";
		
		$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
		$iblockID = false; 
		if ($arIBlock = $rsIBlock->Fetch())
		{			
			CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/header.php', array("ORDERS_IBLOCK_ID" => $iblockID));			
			
			$prop_count = 1;
			$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID));
			while ($arr=$rsProp->Fetch())
			{
				CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/header.php', array("PROP_".$prop_count => $arr['ID']));
				$prop_count++;
			}
			
		}
	}
	return;
}

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/orders.xml"; 
$iblockCode = "orders".WIZARD_SITE_ID; 
$iblockType = "orders"; 

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
		"orders",
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
		'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => GetMessage("WZD_OPTION_ORDER_14"), ), 
		'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'DELETE_WITH_DETAIL' =>'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 
		'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
		'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ),
		'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
		'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 
		'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 
		'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 
		'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 
		'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 
		'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
		"CODE" => "orders",		 
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
	
WizardServices::IncludeServiceLang("orders.php", $lang);

CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_ORDER_14")
		.'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_ORDER_1")
		.'--,--ACTIVE_FROM--#--'.GetMessage("WZD_OPTION_ORDER_2")
		.'--,--ACTIVE_TO--#--'.GetMessage("WZD_OPTION_ORDER_3")
		.'--,--NAME--#--'.GetMessage("WZD_OPTION_ORDER_4")
		.'--,--PROPERTY_'.$arProperty["CITY_OTKUDA"].'--#--'.GetMessage("WZD_OPTION_ORDER_23")
		.'--,--PROPERTY_'.$arProperty["FROM"].'--#--'.GetMessage("WZD_OPTION_ORDER_5")		
		.'--,--PROPERTY_'.$arProperty["FROM_HOUSE"].'--#--'.GetMessage("WZD_OPTION_ORDER_15")
		.'--,--PROPERTY_'.$arProperty["FROM_HOUSING"].'--#--'.GetMessage("WZD_OPTION_ORDER_16")
		.'--,--PROPERTY_'.$arProperty["FROM_BUILDING"].'--#--'.GetMessage("WZD_OPTION_ORDER_17")
		.'--,--PROPERTY_'.$arProperty["FROM_PORCH"].'--#--'.GetMessage("WZD_OPTION_ORDER_18")
		.'--,--PROPERTY_'.$arProperty["CITY_KUDA"].'--#--'.GetMessage("WZD_OPTION_ORDER_24")	
		.'--,--PROPERTY_'.$arProperty["TO"].'--#--'.GetMessage("WZD_OPTION_ORDER_6")		
		.'--,--PROPERTY_'.$arProperty["TO_HOUSE"].'--#--'.GetMessage("WZD_OPTION_ORDER_15")
		.'--,--PROPERTY_'.$arProperty["TO_HOUSING"].'--#--'.GetMessage("WZD_OPTION_ORDER_16")
		.'--,--PROPERTY_'.$arProperty["TO_BUILDING"].'--#--'.GetMessage("WZD_OPTION_ORDER_17")
		.'--,--PROPERTY_'.$arProperty["TO_PORCH"].'--#--'.GetMessage("WZD_OPTION_ORDER_18")		
		.'--,--PROPERTY_'.$arProperty["FIO"].'--#--'.GetMessage("WZD_OPTION_ORDER_7")
		.'--,--PROPERTY_'.$arProperty["TEL"].'--#--'.GetMessage("WZD_OPTION_ORDER_8")
		.'--,--PROPERTY_'.$arProperty["DATA"].'--#--'.GetMessage("WZD_OPTION_ORDER_9")
		.'--,--PROPERTY_'.$arProperty["TIP"].'--#--'.GetMessage("WZD_OPTION_ORDER_10")
		.'--,--PROPERTY_'.$arProperty["DOP"].'--#--'.GetMessage("WZD_OPTION_ORDER_11")
		.'--,--PROPERTY_'.$arProperty["COMM"].'--#--'.GetMessage("WZD_OPTION_ORDER_12")		
		.'--,--PROPERTY_'.$arProperty["FROM_ID"].'--#--'.GetMessage("WZD_OPTION_ORDER_19")
		.'--,--PROPERTY_'.$arProperty["TO_ID"].'--#--'.GetMessage("WZD_OPTION_ORDER_20")
		.'--,--PROPERTY_'.$arProperty["TIP_SERVICE"].'--#--'.GetMessage("WZD_OPTION_ORDER_21")
		.'--,--PROPERTY_'.$arProperty["STATUS_ORDER"].'--#--'.GetMessage("WZD_OPTION_ORDER_22")
				
		.'--,--SORT--#--'.GetMessage("WZD_OPTION_ORDER_13").'--;--', ));
CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'ACTIVE_FROM,PROPERTY_'.$arProperty["FIO"].',PROPERTY_'.$arProperty["TO"].',PROPERTY_'.$arProperty["FROM"].',PROPERTY_'.$arProperty["TEL"].',SORT', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/header.php', array("ORDERS_IBLOCK_ID" => $iblockID));			
			
$prop_count = 1;
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID));
while ($arr=$rsProp->Fetch())
{
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/header.php', array("PROP_".$prop_count => $arr['ID']));
	$prop_count++;
}

?>