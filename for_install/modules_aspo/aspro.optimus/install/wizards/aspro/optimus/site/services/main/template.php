<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;

if(!WIZARD_INSTALL_DEMO_DATA){
	return;
}

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

// copy files
CopyDirFiles(
	WIZARD_TEMPLATE_ABSOLUTE_PATH,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);

// replace macros SITE_DIR & SITE_ID
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("SITE_ID" => WIZARD_SITE_ID));
CWizardUtil::ReplaceMacros($bitrixTemplateDir."js/general.js", Array("SITE_DIR" => WIZARD_SITE_DIR));

// socials
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_VK" => $wizard->GetVar("shopVk")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_ODNOKLASSNIKI" => $wizard->GetVar("shopOdnoklassniki")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_FACEBOOK" => $wizard->GetVar("shopFacebook")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_TWITTER" => $wizard->GetVar("shopTwitter")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_MAILRU" => $wizard->GetVar("shopMailru")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_INSTAGRAM" => $wizard->GetVar("shopInstagram")));
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, array("SITE_YOUTUBE" => $wizard->GetVar("shopYouTube")));

// attach template to default site
if($arSite = CSite::GetByID(WIZARD_SITE_ID)->Fetch()){
	$obTemplate = CSite::GetTemplateList(WIZARD_SITE_ID);
	$arTemplates = array();
	$found = false;
	while ($arTemplate = $obTemplate->Fetch()){
		if(!$found && !strlen($arTemplate["CONDITION"])){
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID;
			$found = true;
		}
		if($arTemplate["TEMPLATE"] == "empty"){
			continue;
		}
		$arTemplates[]= $arTemplate;
	}
	if (!$found){
		$arTemplates[]= array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID);
	}

	$obSite = new CSite();
	$arFields = array("TEMPLATE" => $arTemplates, "DIR" => str_replace('//', '/', str_replace('//', '/', '/'.$arSite["DIR"].'/')));
	$obSite->Update(WIZARD_SITE_ID, $arFields);
}

COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID, false, WIZARD_SITE_ID);
?>
