<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("bejetstore", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

//update iblocks, demo discount and precet
$shopLocalization = $wizard->GetVar("shopLocalization");

if ($_SESSION["WIZARD_CATALOG_IBLOCK_ID"])
{
	$IBLOCK_CATALOG_ID = $_SESSION["WIZARD_CATALOG_IBLOCK_ID"];
	unset($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]);
}
if ($_SESSION["WIZARD_OFFERS_IBLOCK_ID"])
{
	$IBLOCK_OFFERS_ID = $_SESSION["WIZARD_OFFERS_IBLOCK_ID"];
	unset($_SESSION["WIZARD_OFFERS_IBLOCK_ID"]);
}
//reference update
/*$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => "clothes_colors", "TYPE" => "references"));
if ($arIBlock = $rsIBlock->Fetch())
{
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		$ib = new CIBlock;
		$ib->Update($arIBlock["ID"], array("XML_ID" => "clothes_colors_".WIZARD_SITE_ID));
	}
}*/

if ($IBLOCK_OFFERS_ID)
{
	$iblockCodeOffers = "clothes_offers_".WIZARD_SITE_ID;
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array (
			'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ),
			'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ),
			'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ),
			'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ),
			'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ),
			'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ),
			'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'text', ),
			'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ),
			'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), ),
		"CODE" => "clothes_offers",
		"XML_ID" => $iblockCodeOffers
	);
	$iblock->Update($IBLOCK_OFFERS_ID, $arFields);
}

if ($IBLOCK_CATALOG_ID)
{
	$iblockCode = "clothes_".WIZARD_SITE_ID;
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), ),
		"CODE" => "clothes",
		"XML_ID" => $iblockCode
	);
	$iblock->Update($IBLOCK_CATALOG_ID, $arFields);

	if ($IBLOCK_OFFERS_ID)
	{
		$ID_SKU = CCatalog::LinkSKUIBlock($IBLOCK_CATALOG_ID, $IBLOCK_OFFERS_ID);

		$rsCatalogs = CCatalog::GetList(
			array(),
			array('IBLOCK_ID' => $IBLOCK_OFFERS_ID),
			false,
			false,
			array('IBLOCK_ID')
		);
		if ($arCatalog = $rsCatalogs->Fetch())
		{
			CCatalog::Update($IBLOCK_OFFERS_ID,array('PRODUCT_IBLOCK_ID' => $IBLOCK_CATALOG_ID,'SKU_PROPERTY_ID' => $ID_SKU));
		}
		else
		{
			CCatalog::Add(array('IBLOCK_ID' => $IBLOCK_OFFERS_ID, 'PRODUCT_IBLOCK_ID' => $IBLOCK_CATALOG_ID, 'SKU_PROPERTY_ID' => $ID_SKU));
		}
	}

	//user fields for sections
	$arLanguages = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
		$arLanguages[] = $arLanguage["LID"];

	$arUserFields = array("UF_BROWSER_TITLE", "UF_KEYWORDS", "UF_META_DESCRIPTION");
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

		$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => 'IBLOCK_'.$IBLOCK_CATALOG_ID.'_SECTION', "FIELD_NAME" => $userField));
		if ($arRes = $dbRes->Fetch())
		{
			$userType = new CUserTypeEntity();
			$userType->Update($arRes["ID"], $arProperty);
		}
		//if($ex = $APPLICATION->GetException())
			//$strError = $ex->GetString();
	}

//demo discount
	$dbDiscount = CCatalogDiscount::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
	if(!($dbDiscount->Fetch()))
	{
		if (CModule::IncludeModule("iblock"))
		{
			$dbSect = CIBlockSection::GetList(Array(), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "CODE" => "underwear", "IBLOCK_SITE_ID" => WIZARD_SITE_ID));
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
				"CHILDREN" => Array(Array("CLASS_ID" => "CondIBSection", "DATA" => Array("logic" => "Equal", "value" => $sofasSectId)))
			)
		);
		CCatalogDiscount::Add($arF);
	}
//precet
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "CODE"=>"SALELEADER"));
	$arFields = array();
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";
	}
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "CODE"=>"NEWPRODUCT"));
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";
	}
	$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "CODE"=>"SPECIALOFFER"));
	while($arProperty = $dbProperty->GetNext())
	{
		$arFields["find_el_property_".$arProperty["ID"]] = "";
	}
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/admin_lib.php");
	CAdminFilter::AddPresetToBase( array(
			"NAME" => GetMessage("WIZ_PRECET"),
			"FILTER_ID" => "tbl_product_admin_".md5($iblockType.".".$IBLOCK_CATALOG_ID)."_filter",
			"LANGUAGE_ID" => $lang,
			"FIELDS" => $arFields
		)
	);
	CUserOptions::SetOption("filter", "tbl_product_admin_".md5($iblockType.".".$IBLOCK_CATALOG_ID)."_filter", array("rows" => "find_el_name, find_el_active, find_el_timestamp_from, find_el_timestamp_to"), true);

	CAdminFilter::SetDefaultRowsOption("tbl_product_admin_".md5($iblockType.".".$IBLOCK_CATALOG_ID)."_filter", array("miss-0","IBEL_A_F_PARENT"));

//delete 1c props
	$arPropsToDelete = array("CML2_TAXES", "CML2_BASE_UNIT", "CML2_TRAITS", "CML2_ATTRIBUTES", "CML2_ARTICLE", "CML2_BAR_CODE", "CML2_FILES", "CML2_MANUFACTURER", "CML2_PICTURES");
	foreach ($arPropsToDelete as $code)
	{
		$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "XML_ID"=>$code));
		if($arProperty = $dbProperty->GetNext())
		{
			CIBlockProperty::Delete($arProperty["ID"]);
		}
		if ($IBLOCK_OFFERS_ID)
		{
			$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_OFFERS_ID, "XML_ID"=>$code));
			if($arProperty = $dbProperty->GetNext())
			{
				CIBlockProperty::Delete($arProperty["ID"]);
			}
		}
	}

	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_CATALOG_ID));
	while ($prop_fields = $properties->GetNext())
	{
		$arCatalogProps[ $prop_fields["CODE"] ] = $prop_fields["ID"];
	}

	WizardServices::IncludeServiceLang("catalog3.php", $lang);

	if(!empty($arCatalogProps)){
		$array = array ( "tabs" => "edit1--#--".GetMessage("WZD_OPTION_CATALOG_1")."--,--edit1_csection1--#----".GetMessage("WZD_OPTION_CATALOG_2")."--,--ACTIVE--#--".GetMessage("WZD_OPTION_CATALOG_2")."--,--ACTIVE_FROM--#--".GetMessage("WZD_OPTION_CATALOG_3")."--,--ACTIVE_TO--#--".GetMessage("WZD_OPTION_CATALOG_4")."--,--edit1_csection2--#----".GetMessage("WZD_OPTION_CATALOG_5")."--,--NAME--#--*".GetMessage("WZD_OPTION_CATALOG_5")."--,--CODE--#--*".GetMessage("WZD_OPTION_CATALOG_6")."--,--edit1_csection3--#----".GetMessage("WZD_OPTION_CATALOG_7")
			."--,--PROPERTY_".$arCatalogProps["ARTNUMBER"]."--#--".GetMessage("WZD_OPTION_CATALOG_8")
			."--,--PROPERTY_".$arCatalogProps["MANUFACTURER"]."--#--".GetMessage("WZD_OPTION_CATALOG_9")
			."--,--PROPERTY_".$arCatalogProps["BRAND"]."--#--".GetMessage("WZD_OPTION_CATALOG_10")
			."--,--PROPERTY_".$arCatalogProps["MATERIAL"]."--#--".GetMessage("WZD_OPTION_CATALOG_11")
			."--,--PROPERTY_".$arCatalogProps["COLOR"]."--#--".GetMessage("WZD_OPTION_CATALOG_50")
			."--,--PROPERTY_".$arCatalogProps["CHARACTERISTICS"]."--#--".GetMessage("WZD_OPTION_CATALOG_12")
			."--,--edit1_csection6--#----".GetMessage("WZD_OPTION_CATALOG_13")
			."--,--PROPERTY_".$arCatalogProps["MORE_PHOTO"]."--#--".GetMessage("WZD_OPTION_CATALOG_13")
			."--,--edit1_csection4--#----".GetMessage("WZD_OPTION_CATALOG_14")
			."--,--PREVIEW_PICTURE--#--".GetMessage("WZD_OPTION_CATALOG_15")
			."--,--PREVIEW_TEXT--#--".GetMessage("WZD_OPTION_CATALOG_16")
			."--,--edit1_csection5--#----".GetMessage("WZD_OPTION_CATALOG_17")
			."--,--DETAIL_PICTURE--#--".GetMessage("WZD_OPTION_CATALOG_18")
			."--,--DETAIL_TEXT--#--".GetMessage("WZD_OPTION_CATALOG_19")
			."--;--cedit1--#--".GetMessage("WZD_OPTION_CATALOG_20")
			."--,--PROPERTY_".$arCatalogProps["NEWPRODUCT"]."--#--".GetMessage("WZD_OPTION_CATALOG_21")
			."--,--PROPERTY_".$arCatalogProps["SALELEADER"]."--#--".GetMessage("WZD_OPTION_CATALOG_22")
			."--,--PROPERTY_".$arCatalogProps["SALE_RECOMMEND"]."--#--".GetMessage("WZD_OPTION_CATALOG_23")
			."--,--PROPERTY_".$arCatalogProps["BRAND_REF"]."--#--".GetMessage("WZD_OPTION_CATALOG_24")
			."--;--cedit4--#--".GetMessage("WZD_OPTION_CATALOG_25")
			."--,--PROPERTY_".$arCatalogProps["RECOMMEND"]."--#--".GetMessage("WZD_OPTION_CATALOG_26")
			."--,--LINKED_PROP--#--".GetMessage("WZD_OPTION_CATALOG_27")
			."--;--cedit2--#--".GetMessage("WZD_OPTION_CATALOG_28")
			."--,--PROPERTY_".$arCatalogProps["SPECIALOFFER"]."--#--".GetMessage("WZD_OPTION_CATALOG_28")
			."--;--edit2--#--".GetMessage("WZD_OPTION_CATALOG_29")
			."--,--SORT--#--".GetMessage("WZD_OPTION_CATALOG_30")
			."--,--SECTIONS--#--*".GetMessage("WZD_OPTION_CATALOG_31")
			."--;--edit14--#--SEO--,--IPROPERTY_TEMPLATES_ELEMENT_META_TITLE--#--".GetMessage("WZD_OPTION_CATALOG_32")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS--#--".GetMessage("WZD_OPTION_CATALOG_33")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION--#--".GetMessage("WZD_OPTION_CATALOG_34")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE--#--".GetMessage("WZD_OPTION_CATALOG_35")
			."--,--IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE--#----".GetMessage("WZD_OPTION_CATALOG_36")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT--#--".GetMessage("WZD_OPTION_CATALOG_37")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE--#--".GetMessage("WZD_OPTION_CATALOG_38")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME--#--".GetMessage("WZD_OPTION_CATALOG_39")
			."--,--IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE--#----".GetMessage("WZD_OPTION_CATALOG_40")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT--#--".GetMessage("WZD_OPTION_CATALOG_37")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE--#--".GetMessage("WZD_OPTION_CATALOG_38")
			."--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME--#--".GetMessage("WZD_OPTION_CATALOG_39")
			."--,--SEO_ADDITIONAL--#----".GetMessage("WZD_OPTION_CATALOG_41")
			."--,--TAGS--#--".GetMessage("WZD_OPTION_CATALOG_42")
			."--;--cedit3--#--".GetMessage("WZD_OPTION_CATALOG_43")
			."--,--PROPERTY_".$arCatalogProps["BLOG_POST_ID"]."--#--ID ".GetMessage("WZD_OPTION_CATALOG_44")
			."--,--PROPERTY_".$arCatalogProps["BLOG_COMMENTS_CNT"]."--#--".GetMessage("WZD_OPTION_CATALOG_45")
			."--,--PROPERTY_".$arCatalogProps["FORUM_MESSAGE_CNT"]."--#--".GetMessage("WZD_OPTION_CATALOG_46")
			."--,--PROPERTY_".$arCatalogProps["vote_count"]."--#--".GetMessage("WZD_OPTION_CATALOG_47")
			."--,--PROPERTY_".$arCatalogProps["rating"]."--#--".GetMessage("WZD_OPTION_CATALOG_48")
			."--;--edit10--#--".GetMessage("WZD_OPTION_CATALOG_49")
			."--,--CATALOG--#--*".GetMessage("WZD_OPTION_CATALOG_48")."--;--");
		CUserOptions::SetOption("form", "form_element_".$IBLOCK_CATALOG_ID, $array);
	}
	

	COption::SetOptionString("catalog", "show_catalog_tab_with_offers", "Y");

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("SITE_ID" => WIZARD_SITE_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/filter_array.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_inc.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/campaign/detail.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brand/detail.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
	/*CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/bitrix/components/bejetstore/catalog/templates/.default/brand.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/bitrix/components/bejetstore/catalog/templates/.default/campaign.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/bitrix/components/bejetstore/catalog/templates/.default/tabs.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));*/
	//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/bitrix/templates/bejetstore_purple_white/header.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/sect_sidebar.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/viewed_product.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
}
?>