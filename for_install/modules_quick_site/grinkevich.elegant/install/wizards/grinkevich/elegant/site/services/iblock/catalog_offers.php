<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

// Установка прав
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

$wizrdTemplateId = $wizard->GetVar("wizTemplateID");
if (!in_array($wizrdTemplateId, array("womanizer")))
	$wizrdTemplateId = "womanizer";

// Цвета
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_5.xml";
$iblockCode = "colors_".WIZARD_SITE_ID;
$iblockType = "catalog";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
		COption::SetOptionString("eshop", "demo_deleted", "N", "", WIZARD_SITE_ID);
	}
}

if($iblockID == false)
{
	$IBLOCK_COLORS = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"colors",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($IBLOCK_COLORS)
	{
		$iblock = new CIBlock;
		$arFields = Array(
			"ACTIVE" => "Y",
			"CODE" => "colors",
			"XML_ID" => $iblockCode,
		);

		$iblockID = $IBLOCK_COLORS;

		$iblock->Update($IBLOCK_COLORS, $arFields);

		$arProperty = Array();
		$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $IBLOCK_COLORS));
		while($arProp = $dbProperty->Fetch())
			$arProperty[$arProp["CODE"]] = $arProp["ID"];

		CUserOptions::SetOption("form", "form_element_".$IBLOCK_COLORS, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_5").'--;--', ));
		CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$IBLOCK_COLORS), array ( 'columns' => 'NAME,PROPERTY_'.$arProperty["COLORCODE"].',ACTIVE,SORT,TIMESTAMP_X,ID', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));
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


// Сезонность
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_6.xml";
$iblockCode = "season_".WIZARD_SITE_ID;
$iblockType = "catalog";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
		COption::SetOptionString("eshop", "demo_deleted", "N", "", WIZARD_SITE_ID);
	}
}

if ($iblockID == false)
{
	$IBLOCK_SEASON = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"season",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($IBLOCK_SEASON)
	{
		$iblock = new CIBlock;
		$arFields = Array(
			"ACTIVE" => "Y",
			"CODE" => "season",
			"XML_ID" => $iblockCode,
		);

		$iblockID = $IBLOCK_SEASON;

		$iblock->Update($IBLOCK_SEASON, $arFields);

		CUserOptions::SetOption("form", "form_element_".$IBLOCK_SEASON, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--PREVIEW_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_6").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--;--', ));
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


// Производители
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_7.xml";
$iblockCode = "manufactures_".WIZARD_SITE_ID;
$iblockType = "catalog";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
		COption::SetOptionString("eshop", "demo_deleted", "N", "", WIZARD_SITE_ID);
	}
}

if ($iblockID == false)
{
	$IBLOCK_MANUF = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"manufactures",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($IBLOCK_MANUF)
	{
		$iblock = new CIBlock;
		$arFields = Array(
			"ACTIVE" => "Y",
			"CODE" => "manufactures",
			"XML_ID" => $iblockCode,
		);

		$iblockID = $IBLOCK_MANUF;

		$iblock->Update($IBLOCK_MANUF, $arFields);

		CUserOptions::SetOption("form", "form_element_".$IBLOCK_MANUF, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_7").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_8").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--;--', ));
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

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brand/index.php", array("BRAND_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/".$wizrdTemplateId."/header.php", array("BRAND_IBLOCK_ID" => $iblockID));



?>