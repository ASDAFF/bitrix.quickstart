<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/";

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir.WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID,
	$rewrite = true,
	$recursive = true
);
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH.'/site/template_common/',
	$bitrixTemplateDir.WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID,
	$rewrite = true,
	$recursive = true
);

CWizardUtil::ReplaceMacros($bitrixTemplateDir.WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID."/template_styles.css", Array("SITE_ID" => WIZARD_SITE_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/main_info.php', Array("SITE_ID" => WIZARD_SITE_ID));

if (file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR.'images/logo-bg.png'))
	copy (WIZARD_SITE_PATH.WIZARD_SITE_DIR.'images/logo-bg.png',WIZARD_SITE_PATH."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/images/logo-bg.png");

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
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID;
			$found = true;
		}

		$arTemplates[]= $arTemplate;
	}

	if (!$found)
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 1, "TEMPLATE" => WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID);	
	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}

BXClearCache(true);

COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID,'', WIZARD_SITE_ID);
?>
