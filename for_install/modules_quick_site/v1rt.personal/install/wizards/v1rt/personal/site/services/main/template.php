<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!defined("WIZARD_TEMPLATE_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID;

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);

if(@is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default"))
    @mkdir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/");
CopyDirFiles(dirname(__FILE__)."/files/.default", $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default", true, true);

CWizardUtil::ReplaceMacros($bitrixTemplateDir."/header.php", array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros($bitrixTemplateDir."/footer.php", array("SITE_DIR" => WIZARD_SITE_DIR));

//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		if(!$found && strlen(trim($arTemplate["CONDITION"]))<=0)
		{
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID;
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
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID);

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}

COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID, false, WIZARD_SITE_ID);

$tmplt = WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID;
if($tmplt == "personal_v2_".WIZARD_THEME_ID."_".WIZARD_SITE_ID || $tmplt == "personal_v2_".WIZARD_THEME_ID."_".WIZARD_SITE_ID || $tmplt == "personal_v2_".WIZARD_THEME_ID."_".WIZARD_SITE_ID)
    COption::SetOptionString("v1rt.personal", "v1rt_personal_ver_2_logo", $wizard->GetVar("siteLogo", true));
?>
