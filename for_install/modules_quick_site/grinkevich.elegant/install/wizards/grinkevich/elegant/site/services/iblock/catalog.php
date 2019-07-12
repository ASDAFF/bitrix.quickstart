<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

function ___flog($m)
{
	return true;
	$fp = fopen(dirname(__FILE__) . '/flog', 'a+');
	fwrite($fp, $m . "\r\n");
	fclose($fp);
}


if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_shoes.xml";
$iblockXMLFile2 = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_shoes_price.xml";
$iblockCode = "products_".WIZARD_SITE_ID;
$iblockType = "catalog";

___flog($iblockCode);

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
        ___flog('delete' . $arIBlock["ID"]);
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
		COption::SetOptionString("eshop", "demo_deleted", "N", "", WIZARD_SITE_ID);

		$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => 'IBLOCK_'.$arIBlock["ID"].'_SECTION'));
		while ($arRes = $dbRes->Fetch())
		{
			$userType = new CUserTypeEntity();
			$userType->Delete($arRes["ID"]);
		}
	}
}

$dbResultList = CCatalogGroup::GetList(Array(), Array("CODE" => "BASE"));
if (!($dbResultList->Fetch()))
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

if ($iblockID == false)
{
	___flog('setup');
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
		"products",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);
    ___flog($iblockXMLFile);

	$iblockID2 = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile2,
		"products",
		$iblockType . '_prices',
		WIZARD_SITE_ID,
		$permissions
	);
	___flog($iblockXMLFile2);


	if ($iblockID < 1)
		return;

	___flog('ok');

	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array (
			'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => ''),
			'ACTIVE' => 		array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y'),
			'ACTIVE_FROM' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'ACTIVE_TO' => 		array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'SORT' => 			array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'NAME' => 			array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => ''),
			'PREVIEW_PICTURE' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', )),
			'PREVIEW_TEXT_TYPE' => 	array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text'),
			'PREVIEW_TEXT' => 		array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'DETAIL_PICTURE' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95)),
			'DETAIL_TEXT_TYPE' => 	array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text'),
			'DETAIL_TEXT' => 		array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'XML_ID' => 		array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'CODE' => 			array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', )),
			'TAGS' => 			array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'SECTION_NAME' => 		array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => ''),
			'SECTION_PICTURE' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', )),
			'SECTION_DESCRIPTION_TYPE' => 	array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text'),
			'SECTION_DESCRIPTION' => 		array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'SECTION_DETAIL_PICTURE' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, )),
			'SECTION_XML_ID' => 	array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
			'SECTION_CODE' => 		array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ))
		),
		"CODE" => "products",
		"XML_ID" => $iblockCode
	);

	$iblock->Update($iblockID, $arFields);

//user fields for sections
	$arLanguages = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
		$arLanguages[] = $arLanguage["LID"];

	$arUserFields = array("UF_BROWSER_TITLE", "UF_TITLE_H1", "UF_KEYWORDS", "UF_META_DESCRIPTION");
	foreach ($arUserFields as $userField)
	{
		$arLabelNames = Array();
		foreach($arLanguages as $languageID)
		{
			WizardServices::IncludeServiceLang("property_names.php", $languageID);
			$arLabelNames[$languageID] = GetMessage($userField);
		}

		$arProperty["EDIT_FORM_LABEL"] = $arLabelNames;
		$arProperty["LIST_COLUMN_LABEL"] = $arLabelNames;
		$arProperty["LIST_FILTER_LABEL"] = $arLabelNames;

		$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => 'IBLOCK_'.$iblockID.'_SECTION', "FIELD_NAME" => $userField));
		if ($arRes = $dbRes->Fetch())
		{
			$userType = new CUserTypeEntity();
			$userType->Update($arRes["ID"], $arProperty);
		}
	}

//demo discount
	$dbDiscount = CCatalogDiscount::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
	if(!($dbDiscount->Fetch()))
	{
		if (CModule::IncludeModule("iblock"))
		{
			$dbSect = CIBlockSection::GetList(Array(), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_ID"=>$iblockID, "CODE" => "man_botinki", "IBLOCK_SITE_ID" => WIZARD_SITE_ID));
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
	}
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

WizardServices::IncludeServiceLang("catalog.php", $lang);
//for element edit
CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--,--PROPERTY_'.$arProperty["ARTNUMBER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_5").'--,--PROPERTY_'.$arProperty["MANUFACTURER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_6").'--,--PROPERTY_'.$arProperty["MATERIAL"].'--#--'.GetMessage("WZD_OPTION_CATALOG_7").'--,--PROPERTY_'.$arProperty["COLOR"].'--#--'.GetMessage("WZD_OPTION_CATALOG_8").'--,--PROPERTY_'.$arProperty["SEASON"].'--#--'.GetMessage("WZD_OPTION_CATALOG_9").'--,--PROPERTY_'.$arProperty["SIZE"].'--#--'.GetMessage("WZD_OPTION_CATALOG_10").'--,--PROPERTY_'.$arProperty["KABLUK"].'--#--'.GetMessage("WZD_OPTION_CATALOG_11").'--,--PROPERTY_'.$arProperty["SEX"].'--#--'.GetMessage("WZD_OPTION_CATALOG_12").'--,--PROPERTY_'.$arProperty["SALELEADER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_15").'--,--PROPERTY_'.$arProperty["NEWPRODUCT"].'--#--'.GetMessage("WZD_OPTION_CATALOG_14").'--,--PROPERTY_'.$arProperty["SPECIALOFFER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_13").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_CATALOG_16").'--,--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_17").'--;--cedit2--#--'.GetMessage("WZD_OPTION_CATALOG_18").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_19").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--cedit3--#--'.GetMessage("WZD_OPTION_CATALOG_21").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_22").'--,--PROPERTY_'.$arProperty["MORE_PHOTO"].'--#--'.GetMessage("WZD_OPTION_CATALOG_23").'--;--cedit1--#--'.GetMessage("WZD_OPTION_CATALOG_24").'--,--PROPERTY_'.$arProperty["RECOMMEND"].'--#--'.GetMessage("WZD_OPTION_CATALOG_25").'--;--cedit4--#--'.GetMessage("WZD_OPTION_CATALOG_26").'--,--PROPERTY_'.$arProperty["TITLE"].'--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--,--PROPERTY_'.$arProperty["HEADER1"].'--#--'.GetMessage("WZD_OPTION_CATALOG_28").'--,--PROPERTY_'.$arProperty["KEYWORDS"].'--#--'.GetMessage("WZD_OPTION_CATALOG_29").'--,--PROPERTY_'.$arProperty["META_DESCRIPTION"].'--#--'.GetMessage("WZD_OPTION_CATALOG_30").'--;--', ));
//for section edit
CUserOptions::SetOption("form", "form_section_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_32").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_31").'--,--IBLOCK_SECTION_ID--#--'.GetMessage("WZD_OPTION_CATALOG_33").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_34").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_35").'--,--UF_BROWSER_TITLE--#--'.GetMessage("WZD_OPTION_CATALOG_36").'--,--UF_TITLE_H1--#--'.GetMessage("WZD_OPTION_CATALOG_37").'--,--UF_KEYWORDS--#--'.GetMessage("WZD_OPTION_CATALOG_38").'--,--UF_META_DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_39").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_40").'--,--PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_41").'--,--DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_42").'--;--edit1_csection2--#--'.GetMessage("WZD_OPTION_CATALOG_43").'--,--SECTION_PROPERTY--#--'.GetMessage("WZD_OPTION_CATALOG_44").'--;--edit4--#--'.GetMessage("WZD_OPTION_CATALOG_44").'--;--', ));

CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'DETAIL_PICTURE,PROPERTY_'.$arProperty["ARTNUMBER"].',NAME,CATALOG_GROUP_1,PROPERTY_'.$arProperty["SPECIALOFFER"].',PROPERTY_'.$arProperty["NEWPRODUCT"].',PROPERTY_'.$arProperty["SALELEADER"].'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));
CUserOptions::SetOption("list", "tbl_product_admin_".md5($iblockType.".".$iblockID), array ( 'columns' => 'DETAIL_PICTURE,NAME,CATALOG_GROUP_1,ACTIVE,SORT,CATALOG_QUANTITY,ID,TIMESTAMP_X'.'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));


___flog('opts');

// colors & sizes link

$arProp4Link = array(
	"COLOR"=>"colors",
	"SEASON"=>"season",
	"RECOMMEND" => "elegant",
	"MANUFACTURER" => "manufactures"
);

$arProp4LinkSF = array(
	"COLOR"=>"Y",
	"SEASON"=>"Y",
	"RECOMMEND" => "N",
	"MANUFACTURER" => "Y"
);


$dbProp = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $iblockID));
while($arProp = $dbProp->Fetch()){
	if(!array_key_exists($arProp['CODE'], $arProp4Link)) continue;

	$rsIBlock = CIBlock::GetList(array(), array("CODE" => $arProp4Link[$arProp['CODE']], "XML_ID" => $arProp4Link[$arProp['CODE']]."_".WIZARD_SITE_ID, "TYPE" => 'catalog'));
   	if($arIBlock = $rsIBlock->Fetch()){
		$arFieldsUpdate = Array(
			"LINK_IBLOCK_ID" => $arIBlock['ID'],
			"IBLOCK_ID" => $iblockID,
			"SMART_FILTER"=>$arProp4LinkSF[$arProp['CODE']]
		);

		$ibp = new CIBlockProperty;
		if(!$ibp->Update($arProp['ID'], $arFieldsUpdate))
			return;
	}
}

___flog('props');

// #CATALOG_IBLOCK_ID#
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/sect_left.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.center.menu_ext.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.left.menu_ext.php", array("CATALOG_IBLOCK_ID" => $iblockID));



$wizrdTemplateId = $wizard->GetVar("wizTemplateID");
if (!in_array($wizrdTemplateId, array("womanizer")))
	$wizrdTemplateId = "womanizer";

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/".$wizrdTemplateId."/header.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/".$wizrdTemplateId."/components/bitrix/catalog.smart.filter/index/template.php", array("CATALOG_IBLOCK_ID" => $iblockID));

?>