<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/mobile/";

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/mobile/",
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);

$siteFolder = str_replace(array("\\", "///", "//"), "/", $wizard->GetVar("siteFolder")."/");
$siteFolderFull = str_replace(array("\\", "///", "//"), "/", WIZARD_SITE_DIR.$wizard->GetVar("siteFolder")."/");
WizardServices::ReplaceMacrosRecursive($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/mobile/", Array("SITE_DIR" => $siteFolderFull));

//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if ($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		$arTemplates[]= $arTemplate;
	}

	$arTemplates[]= Array(
				"TEMPLATE" => "mobile",
				"SORT" => 100,
				"CONDITION" => "CSite::InDir('".$siteFolderFull."')",
			);

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}
?>
