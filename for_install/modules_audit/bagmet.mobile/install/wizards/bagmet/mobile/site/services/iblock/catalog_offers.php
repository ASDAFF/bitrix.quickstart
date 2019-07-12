<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("bagmet_mobile", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/catalog_sku.xml";
$iblockCode = "mobile_offers_".WIZARD_SITE_ID;
$iblockType = "offers"; 

 
$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		/*$arCatalog = CCatalog::GetByIDExt($arIBlock["ID"]); 
		if (is_array($arCatalog) && (in_array($arCatalog['CATALOG_TYPE'],array('P','X'))) == true) 
		{
			CCatalog::UnLinkSKUIBlock($arIBlock["ID"]);
			CIBlock::Delete($arCatalog['OFFERS_IBLOCK_ID']);
		}  */
		CIBlock::Delete($arIBlock["ID"]); 
		$iblockID = false; 
		COption::SetOptionString("bagmet_mobile", "demo_deleted", "N", "", WIZARD_SITE_ID);
	}
}              

CModule::IncludeModule("catalog");
   
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

	$IBLOCK_OFFERS_ID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"mobile_offers",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);     

	if ($IBLOCK_OFFERS_ID < 1)
		return;
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ('IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
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
		"CODE" => "mobile_offers",
		"XML_ID" => $iblockCode,
		//"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
	);

	$iblock->Update($IBLOCK_OFFERS_ID, $arFields);
	
	$iblockCodeFur = "mobile_".WIZARD_SITE_ID;
	$iblockTypeFur = "catalog"; 
	
	$rsIBlockFur = CIBlock::GetList(array(), array("XML_ID" => $iblockCodeFur, "TYPE" => $iblockTypeFur));
	if ($arIBlockFur = $rsIBlockFur->Fetch())
	{
		$ID_SKU = CCatalog::LinkSKUIBlock($arIBlockFur["ID"], $IBLOCK_OFFERS_ID); 
	} 
	
	$arCatalog = CCatalog::GetByID($IBLOCK_OFFERS_ID);
	if ($arCatalog)
	{
		CCatalog::Update($IBLOCK_OFFERS_ID,array('PRODUCT_IBLOCK_ID' => $arIBlockFur["ID"],'SKU_PROPERTY_ID' => $ID_SKU));
	}
	else
	{
		CCatalog::Add(array('IBLOCK_ID' => $IBLOCK_OFFERS_ID, 'PRODUCT_IBLOCK_ID' => $arIBlockFur["ID"], 'SKU_PROPERTY_ID' => $ID_SKU));
	}


	$dbOfferProps = CIblock::GetProperties(
		$IBLOCK_OFFERS_ID,
		Array(),
		Array()
	);

	while($arOfferProps = $dbOfferProps->Fetch())
	{
		$arProps[$arOfferProps["XML_ID"]] = $arOfferProps["ID"];
	}


	$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_OFFERS_ID, "CODE"=>"MEMORY_SIZE"));
	$arEnumIds = array();
	while($enum_fields = $property_enums->GetNext())
	{
		$arEnumIds[] = $enum_fields["ID"];
	}

	$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_OFFERS_ID, "CODE"=>"GSM"));
	$arEnumIds2 = array();
	while($enum_fields = $property_enums->GetNext())
	{
		$arEnumIds2[] = $enum_fields["ID"];
	}
//white iphone 5
	$dbElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arIBlockFur["ID"], "XML_ID" => 163), false);
	$arElement = $dbElement->Fetch();
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2]/*, $arProps["329"] => $arEnumIds2[0]*/),
					"SORT" => "300"
				),
			"PRICE" => '28590.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[1]/*, $arProps["329"] => $arEnumIds2[1]*/),
					"SORT" => "200"
				),
			"PRICE" => '26590.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[0]/*, $arProps["329"] => $arEnumIds2[0]*/),
					"SORT" => "100"
				),
			"PRICE" => '24590.00'
		);

//black iphone 5
	$dbElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arIBlockFur["ID"], "XML_ID" => 171), false);
	$arElement = $dbElement->Fetch();
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2]/*, $arProps["329"] => $arEnumIds2[0]*/),
					"SORT" => "300"
				),
			"PRICE" => '28590.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[1]/*, $arProps["329"] => $arEnumIds2[1]*/),
					"SORT" => "200"
				),
			"PRICE" => '26590.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[0]/*, $arProps["329"] => $arEnumIds2[0]*/),
					"SORT" => "100"
				),
			"PRICE" => '24590.00'
		);
//white ipad
	$dbElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arIBlockFur["ID"], "XML_ID" => 205), false);
	$arElement = $dbElement->Fetch();
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[3], $arProps["329"] => $arEnumIds2[0]),
					"SORT" => "400"
				),
			"PRICE" => '21090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[3], $arProps["329"] => $arEnumIds2[1]),
					"SORT" => "300"
				),
			"PRICE" => '17090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2], $arProps["329"] => $arEnumIds2[0]),
					"SORT" => "200"
				),
			"PRICE" => '19090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2], $arProps["329"] => $arEnumIds2[1]),
					"SORT" => "100"
				),
			"PRICE" => '15090.00'
		);
//black ipad
	$dbElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arIBlockFur["ID"], "XML_ID" => 204), false);
	$arElement = $dbElement->Fetch();
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[3], $arProps["329"] => $arEnumIds2[0]),
					"SORT" => "400"
				),
			"PRICE" => '21090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[3], $arProps["329"] => $arEnumIds2[1]),
					"SORT" => "300"
				),
			"PRICE" => '17090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2], $arProps["329"] => $arEnumIds2[0]),
					"SORT" => "200"
				),
			"PRICE" => '19090.00'
		);
	$arOfferElements[] =
		array(
			"PRODUCT" =>
				array(
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"NAME" => $arElement["NAME"],
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array($arProps["CML2_LINK"] =>	$arElement["ID"], $arProps["327"] => $arEnumIds[2], $arProps["329"] => $arEnumIds2[1]),
					"SORT" => "100"
				),
			"PRICE" => '15090.00'
		);

	foreach($arOfferElements as $key => $arOffer)
	{
		$el = new CIBlockElement;
		$elementID = $el->Add($arOffer["PRODUCT"]);
		$elementProduct = CCatalogProduct::Add(array("ID" => $elementID, "QUANTITY" => "0", "QUANTITY_TRACE" => "D", "WEIGHT" => "0"));

		$dbSite = CSite::GetByID(WIZARD_SITE_ID);
		if($arSite = $dbSite -> Fetch())
			$lang = $arSite["LANGUAGE_ID"];
		if(strlen($lang) <= 0)
			$lang = "ru";

		$defCurrency = "EUR";
		if($lang == "ru")
		{
			$shopLocalization = $wizard->GetVar("shopLocalization");
			if ($shopLocalization == "ua")
				$defCurrency = "UAH";
			else
				$defCurrency = "RUB";
		}
		elseif($lang == "en")
			$defCurrency = "USD";

		$elementPrice = CPrice::Add(array("PRODUCT_ID" => $elementID, "PRICE" => $arOffer["PRICE"], "CURRENCY" => $defCurrency, "CATALOG_GROUP_ID" => "1"));
	}

	// form for sku
	$fOfferss = '--PROPERTY_'.$arProps["329"].'--#--'.GetMessage("WZD_OPTION_CATALOG_32").'--,--PROPERTY_'.$arProps["327"].'--#--'.GetMessage("WZD_OPTION_CATALOG_33").'--,--SUB_SORT--#--'.GetMessage("WZD_OPTION_CATALOG_10").'--,';
	$f2 = 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,';
	$f2 .= $fOfferss;
	$f2 .= '--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--';
	$f3 = 'sub_edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--SUB_ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--SUB_NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,';
	$f3 .= $fOfferss;
	$f3 .= '--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--';

	CUserOptions::SetOption("form", "form_element_".$IBLOCK_OFFERS_ID, array ( 'tabs' => $f2, ));
	CUserOptions::SetOption("form", "form_subelement_".$IBLOCK_OFFERS_ID, array ( 'tabs' => $f3, )); 
}
else
{
	$arSites = array(); 
	$db_res = CIBlock::GetSite($IBLOCK_OFFERS_ID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"]; 
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
	}
}             
/*
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
CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_5").'--,--PROPERTY_'.$arProperty["SPECIALOFFER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_10").'--,--PROPERTY_'.$arProperty["NEWPRODUCT"].'--#--'.GetMessage("WZD_OPTION_CATALOG_11").'--,--PROPERTY_'.$arProperty["SALELEADER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_29").'--,--PROPERTY_'.$arProperty["SIZE"].'--#--'.GetMessage("WZD_OPTION_CATALOG_13").'--,--PROPERTY_'.$arProperty["S_SIZE"].'--#--'.GetMessage("WZD_OPTION_CATALOG_14").'--,--PROPERTY_'.$arProperty["ARTNUMBER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_15").'--,--PROPERTY_'.$arProperty["MATERIAL"].'--#--'.GetMessage("WZD_OPTION_CATALOG_16").'--,--PROPERTY_'.$arProperty["MANUFACTURER"].'--#--'.GetMessage("WZD_OPTION_CATALOG_17").'--,--PROPERTY_'.$arProperty["RECOMMEND"].'--#--'.GetMessage("WZD_OPTION_CATALOG_31").'--,--PROPERTY_'.$arProperty["MORE_PHOTO"].'--#--'.GetMessage("WZD_OPTION_CATALOG_18").'--,--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--cedit1--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_6").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_7").'--,--cedit1_csection1--#----'.GetMessage("WZD_OPTION_CATALOG_9").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_CATALOG_30").'--;--', ));

CUserOptions::SetOption("form", "form_section_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_21").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_22").'--,--IBLOCK_SECTION_ID--#--'.GetMessage("WZD_OPTION_CATALOG_23").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_24").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_25").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_28").'--,--PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_26").'--,--DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--;--', ));

CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'DETAIL_PICTURE,PROPERTY_'.$arProperty["ARTNUMBER"].',NAME,CATALOG_GROUP_1,PROPERTY_'.$arProperty["SPECIALOFFER"].',PROPERTY_'.$arProperty["NEWPRODUCT"].',PROPERTY_'.$arProperty["SALELEADER"].'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_inc.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/sect_sidebar.php.php", array("CATALOG_IBLOCK_ID" => $iblockID));   */       
?>