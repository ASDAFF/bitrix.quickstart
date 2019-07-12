<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/iblock_3.xml";
$iblockCode = "shoes_slider_".WIZARD_SITE_ID;
$iblockType = "news";

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
		"slider",
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
			'PREVIEW_PICTURE' => array ('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array (
				'FROM_DETAIL' => 'N',
				'SCALE' => 'Y',
				'WIDTH' => '700',
				'HEIGHT' => '380',
				'IGNORE_ERRORS' => 'N',
				'METHOD' => 'resample',
				'COMPRESSION' => 95,
				'DELETE_WITH_DETAIL' => 'N',
				'UPDATE_WITH_DETAIL' => 'N',
			)),
		),
		"CODE" => "slider",
		"XML_ID" => $iblockCode,
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

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";

$arProperty = Array();
$dbProperty = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $iblockID));
while($arProp = $dbProperty->Fetch())
	$arProperty[$arProp["CODE"]] = $arProp["ID"];

WizardServices::IncludeServiceLang("slider.php", $lang);

CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_SLIDER_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_SLIDER_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_SLIDER_3").'--,--PREVIEW_PICTURE--#--'.GetMessage("WZD_OPTION_SLIDER_4").'--,--SORT--#--'.GetMessage("WZD_OPTION_SLIDER_5").'--,--PROPERTY_'.$arProperty["LINK"].'--#--'.GetMessage("WZD_OPTION_SLIDER_6").'--;--', ));
CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'NAME,ACTIVE,PREVIEW_PICTURE,PROPERTY_'.$arProperty["LINK"].'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));

$wizrdTemplateId = $wizard->GetVar("wizTemplateID");
if (!in_array($wizrdTemplateId, array("womanizer")))
	$wizrdTemplateId = "womanizer";

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/".$wizrdTemplateId."/header.php", array("SLIDER_IBLOCK_ID" => $iblockID));

?>