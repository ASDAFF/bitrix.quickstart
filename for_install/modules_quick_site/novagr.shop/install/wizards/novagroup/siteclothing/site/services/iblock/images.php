<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/novagr_standard_images.xml";
$iblockXML = "18";
$iblockCode = "novagr_standard_images";
$iblockType = "catalog";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXML, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_REINSTALL_DATA)
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
	
$ib = new CIBlock;
$arF = array(
"NAME" => "fashions",
"CODE" => $iblockCode,
"EXTERNAL_ID" => $iblockXML,
"IBLOCK_TYPE_ID" => $iblockType,
"SITE_ID" => array(WIZARD_SITE_ID),
"VERSION" => 2,
'GROUP_ID' => $permissions
);
$iblockID = $ib->Add($arF);
	
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockXML,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($iblockID < 1)
		return;
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
/*
$arReplace = array(
	'NEWS_ID' => $iblockID
);

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."sect_bottom.php", $arReplace);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."news/index.php", $arReplace);
*/
?>