<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

if(COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
	return;
if (!CModule::IncludeModule("blog")) return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/blogs.xml"; 
$iblockXML = "21";
$iblockCode = "blogs"; 
$iblockType = "articles"; 

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
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockXML,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

$arFields = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "NAME" => "Group"
);
$newID = CBlogGroup::Add($arFields);

$arFields = array(
    "NAME" => 'vv',
    "DESCRIPTION" => 'desc',
    "GROUP_ID" => $newID,
    "ENABLE_IMG_VERIF" => 'Y',
    "EMAIL_NOTIFY" => 'Y',
    "ENABLE_RSS" => "Y",
    "URL" => "blogs",
    "ACTIVE" => "Y",
    "OWNER_ID" => 1
);
$newID = CBlog::Add($arFields);


//$arIMAGE = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/images/web_form.gif");
//$arIMAGE["MODULE_ID"] = "form";

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