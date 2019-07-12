<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

//echo "WIZARD_SITE_ID=".WIZARD_SITE_ID." <br />";
//echo "WIZARD_SITE_PATH=".WIZARD_SITE_PATH." <br /> ";
//echo "WIZARD_RELATIVE_PATH=".WIZARD_RELATIVE_PATH." <br /> ";
//echo "WIZARD_ABSOLUTE_PATH=".WIZARD_ABSOLUTE_PATH." <br /> ";
//echo "WIZARD_TEMPLATE_ID=".WIZARD_TEMPLATE_ID." <br /> ";
//echo "WIZARD_TEMPLATE_RELATIVE_PATH=".WIZARD_TEMPLATE_RELATIVE_PATH." <br /> ";
//echo "WIZARD_TEMPLATE_ABSOLUTE_PATH=".WIZARD_TEMPLATE_ABSOLUTE_PATH." <br /> ";
//echo "WIZARD_THEME_ID=".WIZARD_THEME_ID." <br /> ";
//echo "WIZARD_THEME_RELATIVE_PATH=".WIZARD_THEME_RELATIVE_PATH." <br /> ";
//echo "WIZARD_THEME_ABSOLUTE_PATH=".WIZARD_THEME_ABSOLUTE_PATH." <br /> ";
//echo "WIZARD_SERVICE_RELATIVE_PATH=".WIZARD_SERVICE_RELATIVE_PATH." <br /> ";
//echo "WIZARD_SERVICE_ABSOLUTE_PATH=".WIZARD_SERVICE_ABSOLUTE_PATH." <br /> ";
//echo "WIZARD_IS_RERUN=".WIZARD_IS_RERUN." <br /> ";
//die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$mainTemplateName = WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
$mainTemplateDir = WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/templates/".$mainTemplateName;
$bitrixTemplateDir = WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/templates";


CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH."/main-".WIZARD_THEME_ID,
	$mainTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);


//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if ($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		if(!$found && strlen(trim($arTemplate["CONDITION"]))<=0)
		{
			$arTemplate["TEMPLATE"] = $mainTemplateName;
			$found = true;
		}
		if($arTemplate["TEMPLATE"] == "empty")
		{
			$foundEmpty = true;
			continue;
		}
		$arTemplates[]= $arTemplate;
	}

	if (!$found)
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => $mainTemplateName);

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}


//die;


$arrReplace = array("TEMPLATE_NAME"=>$mainTemplateName);
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, $arrReplace);
?>
