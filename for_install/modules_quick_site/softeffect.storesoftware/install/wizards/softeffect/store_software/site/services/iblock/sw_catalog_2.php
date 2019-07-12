<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;
	
if(COption::GetOptionString("softeffect.storesoftware", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;
	
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
	
$iblockXMLFile = str_replace($_SERVER["DOCUMENT_ROOT"], '', WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/sw_catalog/software.xml"); 
$iblockXMLFilePrices = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/sw_catalog/software_price.xml"; 
$iblockCode = "sw_software"; 
$iblockType = "sw_catalog"; 

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		$arCatalog = CCatalog::GetByIDExt($arIBlock["ID"]); 
		if (is_array($arCatalog) && (in_array($arCatalog['CATALOG_TYPE'],array('P','X'))) == true) 
		{
			CCatalog::UnLinkSKUIBlock($arIBlock["ID"]);
			CIBlock::Delete($arCatalog['OFFERS_IBLOCK_ID']);
		}
		CIBlock::Delete($arIBlock["ID"]); 
		$iblockID = false; 
		COption::SetOptionString("softeffect.storesoftware", "demo_deleted", "N", "", WIZARD_SITE_ID);
		
		$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => 'IBLOCK_'.$arIBlock["ID"].'_SECTION'));
		while ($arRes = $dbRes->Fetch())
		{
			$userType = new CUserTypeEntity();
			$userType->Delete($arRes["ID"]);
		}
	}
}

$dbResultList = CCatalogGroup::GetList(Array(), Array("CODE" => "BASE"));
if(!($dbResultList->Fetch()))
{
	$arFields = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
	{
		WizardServices::IncludeServiceLang("catalog.php", $arLanguage["ID"]);
		$arFields["USER_LANG"][$arLanguage["ID"]] = GetMessage("WIZ_PRICE_NAME");
	}
	$arFields["BASE"] = "Y";
	$arFields["SORT"] = 100;
	$arFields["NAME"] = "BASE";
	$arFields["USER_GROUP"] = Array(1, 2);
	$arFields["USER_GROUP_BUY"] = Array(1, 2);
	CCatalogGroup::Add($arFields);
}


if($iblockID == false)
{
	$permissions = Array(
			"1" => "X",
			"2" => "R"
		);
	$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "sale_administrator"));
	if($arGroup = $dbGroup -> Fetch())
	{
		$permissions[$arGroup["ID"]] = 'W';
	}
	$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
	if($arGroup = $dbGroup -> Fetch())
	{
		$permissions[$arGroup["ID"]] = 'W';
	}
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockCode,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);
	$iblockID1 = WizardServices::ImportIBlockFromXML(
		$iblockXMLFilePrices,
		$iblockCode,
		$iblockType."_prices",
		WIZARD_SITE_ID,
		$permissions
	);

	if ($iblockID < 1)
		return;
	
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		'FIELDS' => array(
			// elements
			'IBLOCK_SECTION' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'ACTIVE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'Y'
			),
			'ACTIVE_FROM' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => '=today',
			),
			'ACTIVE_TO' => array (
				'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''
			),
			'SORT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'NAME' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => ''
			),
			'PREVIEW_PICTURE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => array (
					'FROM_DETAIL' => 'Y',
					'SCALE' => 'Y',
					'WIDTH' => '118',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 100,
					'DELETE_WITH_DETAIL' => 'Y',
					'UPDATE_WITH_DETAIL' => 'Y'
				)
			),
			'PREVIEW_TEXT_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'html'
			),
			'PREVIEW_TEXT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'DETAIL_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95
				)
			),
			'DETAIL_TEXT_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'html'
			),
			'DETAIL_TEXT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'XML_ID' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'CODE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => array (
					'UNIQUE' => 'Y',
					'TRANSLITERATION' => 'Y',
					'TRANS_LEN' => 100,
					'TRANS_CASE' => 'L',
					'TRANS_SPACE' => '_',
					'TRANS_OTHER' => '_',
					'TRANS_EAT' => 'Y',
					'USE_GOOGLE' => 'N',
				)
			),
			'TAGS' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			
			// sections
			'SECTION_NAME' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'FROM_DETAIL' => 'N',
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95,
					'DELETE_WITH_DETAIL' => 'N',
					'UPDATE_WITH_DETAIL' => 'N'
				)
			),
			'SECTION_DESCRIPTION_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'text'
			),
			'SECTION_DESCRIPTION' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_DETAIL_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95
				)
			),
			'SECTION_XML_ID' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_CODE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'UNIQUE' => 'Y',
					'TRANSLITERATION' => 'Y',
					'TRANS_LEN' => 100,
					'TRANS_CASE' => 'L',
					'TRANS_SPACE' => '_',
					'TRANS_OTHER' => '_',
					'TRANS_EAT' => 'Y',
					'USE_GOOGLE' => 'N'
				)
			)
		),
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		//"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
	);
	
	$iblock->Update($iblockID, $arFields);
	
	//user fields for sections	
	$arLanguages = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
		$arLanguages[] = $arLanguage["LID"];
	
	$arUserFields = array("UF_MORE_PHOTO", "UF_CHAK", "UF_CAN", "UF_H1", "UF_TITLE", "UF_DESCR", "UF_KEYW", "UF_TEXT_MINI", "UF_NAME_RUS", "UF_LOGO_ALLBRANDS", "UF_DEMO_LINK");
	foreach ($arUserFields as $userField) {
		$arLabelNames = Array();
		foreach($arLanguages as $languageID) {
			WizardServices::IncludeServiceLang("property_names.php", $languageID);
			$arLabelNames[$languageID] = GetMessage($userField);
		}
	
		$arProperty["EDIT_FORM_LABEL"] = $arLabelNames;
		$arProperty["LIST_COLUMN_LABEL"] = $arLabelNames;
		$arProperty["LIST_FILTER_LABEL"] = $arLabelNames;
	
		$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => 'IBLOCK_'.$iblockID.'_SECTION', "FIELD_NAME" => $userField));
		if ($arRes = $dbRes->Fetch()) {
			$userType = new CUserTypeEntity();
			$userType->Update($arRes["ID"], $arProperty);
		}
		//if($ex = $APPLICATION->GetException())
			//$strError = $ex->GetString();
	}

	/*
	//demo discount
	$dbDiscount = CCatalogDiscount::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
	if(!($dbDiscount->Fetch()))
	{
		if (CModule::IncludeModule("iblock"))
		{
			$dbSect = CIBlockSection::GetList(Array(), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_ID"=>$iblockID, "CODE" => "sofas", "IBLOCK_SITE_ID" => WIZARD_SITE_ID));
			if ($arSect = $dbSect->Fetch())
				$sofasSectId = $arSect["ID"];
		}
		$dbSite = CSite::GetByID(WIZARD_SITE_ID);
		if($arSite = $dbSite -> Fetch())
			$lang = $arSite["LANGUAGE_ID"];
		$defCurrency = "EUR";
		if($lang == "ru")
			$defCurrency = "RUB";
		elseif($lang == "en")
			$defCurrency = "USD";
		$arF = Array (
			"SITE_ID" => WIZARD_SITE_ID,
			"ACTIVE" => "Y",
			//"ACTIVE_FROM" => ConvertTimeStamp(mktime(0,0,0,12,15,2011), "FULL"),
			//"ACTIVE_TO" => ConvertTimeStamp(mktime(0,0,0,03,15,2012), "FULL"),
			"RENEWAL" => "N",
			"NAME" => GetMessage("WIZ_DISCOUNT"),
			"SORT" => 100,
			"MAX_DISCOUNT" => 0,
			"VALUE_TYPE" => "P",
			"VALUE" => 10,
			"CURRENCY" => $defCurrency,
			"CONDITIONS" => Array ( 
				"CLASS_ID" => "CondGroup", 
				"DATA" => Array("All" => "OR", "True" => "True"),
				"CHILDREN" => Array("0" => Array("CLASS_ID" => "CondIBSection", "DATA" => Array("logic" => "Equal", "value" => $sofasSectId)))
			)
		);
		CCatalogDiscount::Add($arF);
	}
	//precet	
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "CODE"=>"SALELEADER"));
	$arFields = array();
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";	  
	}
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "CODE"=>"NEWPRODUCT"));
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";	  
	}
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "CODE"=>"SPECIALOFFER"));
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";	  
	}
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/admin_lib.php");
	CAdminFilter::AddPresetToBase( array(
			"NAME" => GetMessage("WIZ_PRECET"),
			"FILTER_ID" => "tbl_product_admin_".md5($iblockType.".".$iblockID)."_filter",
			"LANGUAGE_ID" => $lang,
			"FIELDS" => $arFields
		)
	);	
	CUserOptions::SetOption("filter", "tbl_product_admin_".md5($iblockType.".".$iblockID)."_filter", array("rows" => "find_el_name, find_el_active, find_el_timestamp_from, find_el_timestamp_to"), true);
		
	CAdminFilter::SetDefaultRowsOption("tbl_product_admin_".md5($iblockType.".".$iblockID)."_filter", array("miss-0","IBEL_A_F_PARENT"));
	
	
	//delete 1c props
	$arPropsToDelete = array("CML2_TAXES", "CML2_BASE_UNIT", "CML2_TRAITS", "CML2_ATTRIBUTES", "CML2_ARTICLE", "CML2_BAR_CODE");
	foreach ($arPropsToDelete as $code)
	{
		$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "XML_ID"=>$code));
		if($arProperty = $dbProperty->GetNext())
		{
			CIBlockProperty::Delete($arProperty["ID"]);	  
		}
	}*/
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

/*$arProperty = Array();
$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $iblockID));
while($arProp = $dbProperty->Fetch())
	$arProperty[$arProp["CODE"]] = $arProp["ID"];
	
$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
WizardServices::IncludeServiceLang("catalog.php", $lang);
//for element edit
CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--,--PROPERTY_'.$arProperty["TITLE"].'--#--'.GetMessage("WZD_OPTION_CATALOG_35").'--,--PROPERTY_'.$arProperty["HEADER1"].'--#--'.GetMessage("WZD_OPTION_CATALOG_36").'--,--PROPERTY_'.$arProperty["KEYWORDS"].'--#--'.GetMessage("WZD_OPTION_CATALOG_37").'--,--PROPERTY_'.$arProperty["META_DESCRIPTION"].'--#--'.GetMessage("WZD_OPTION_CATALOG_38").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--PROPERTY_'.$arProperty["SALELEADER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_29").'--,--PROPERTY_'.$arProperty["NEWPRODUCT"].'--#--'.GetMessage("WZD_OPTION_CATALOG_11").'--,--PROPERTY_'.$arProperty["SPECIALOFFER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_10").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_6").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_7").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_5").'--,--PROPERTY_'.$arProperty["MORE_PHOTO"].'--#--'.GetMessage("WZD_OPTION_CATALOG_18").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_CATALOG_39").'--,--IBLOCK_ELEMENT_PROPERTY--#--'.GetMessage("WZD_OPTION_CATALOG_32").'--,--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--cedit1--#--'.GetMessage("WZD_OPTION_CATALOG_33").'--,--PROPERTY_'.$arProperty["RECOMMEND"].'--#--'.GetMessage("WZD_OPTION_CATALOG_31").'--;--edit8--#--'.GetMessage("WZD_OPTION_CATALOG_34").'--,--OFFERS--#--'.GetMessage("WZD_OPTION_CATALOG_34").'--;--', ));
//for section edit
CUserOptions::SetOption("form", "form_section_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_21").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_22").'--,--IBLOCK_SECTION_ID--#--'.GetMessage("WZD_OPTION_CATALOG_23").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_24").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_25").'--,--UF_BROWSER_TITLE--#--'.GetMessage("WZD_OPTION_CATALOG_35").'--,--UF_TITLE_H1--#--'.GetMessage("WZD_OPTION_CATALOG_36").'--,--UF_KEYWORDS--#--'.GetMessage("WZD_OPTION_CATALOG_37").'--,--UF_META_DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_38").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_28").'--,--PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_26").'--,--DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--;--edit1_csection2--#--'.GetMessage("WZD_OPTION_CATALOG_40").'--,--SECTION_PROPERTY--#--'.GetMessage("WZD_OPTION_CATALOG_41").'--;--edit4--#--'.GetMessage("WZD_OPTION_CATALOG_41").'--;--', ));

CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'DETAIL_PICTURE,PROPERTY_'.$arProperty["ARTNUMBER"].',NAME,CATALOG_GROUP_1,PROPERTY_'.$arProperty["SPECIALOFFER"].',PROPERTY_'.$arProperty["NEWPRODUCT"].',PROPERTY_'.$arProperty["SALELEADER"].'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));
CUserOptions::SetOption("list", "tbl_product_admin_".md5($iblockType.".".$iblockID), array ( 'columns' => 'DETAIL_PICTURE,NAME,CATALOG_GROUP_1,ACTIVE,SORT,CATALOG_QUANTITY,ID,TIMESTAMP_X'.'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));*/

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/admin-config/config.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/search/index.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/section.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/category.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/detail.php", array($iblockCode => $iblockID));
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."/header.php", array($iblockCode => $iblockID));

$dbProps = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$iblockID));
while ($arProps = $dbProps->Fetch()) {
	if ($arProps['SORT']>999) {
		CIBlockSectionPropertyLink::Delete(0, $arProps['ID']);
		CIBlockSectionPropertyLink::Add(0, $arProps['ID'], array('SMART_FILTER'=>'Y'));
	}
}
?>