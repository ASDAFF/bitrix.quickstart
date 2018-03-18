<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$templateDir = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID;
CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH,
	$_SERVER["DOCUMENT_ROOT"].$templateDir,
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

$mainTemplateDir = BX_PERSONAL_ROOT."/templates/main_".WIZARD_SITE_ID;
CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WIZARD_RELATIVE_PATH."/site/working_templates/main/themes/".WIZARD_THEME_ID,
	$_SERVER["DOCUMENT_ROOT"].$mainTemplateDir,
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

$icon_path = $_SERVER["DOCUMENT_ROOT"].WIZARD_RELATIVE_PATH."/site/working_templates/main/themes/".WIZARD_THEME_ID."/favicon.ico";
RewriteFile($_SERVER["DOCUMENT_ROOT"]."/echo.php", $_SERVER["DOCUMENT_ROOT"].WIZARD_SITE_DIR);
CopyDirFiles(
	$icon_path,
	$_SERVER["DOCUMENT_ROOT"].WIZARD_SITE_DIR."favicon.ico",
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false
);

$wizard =& $this->GetWizard();
$companyLogo = $wizard->GetVar("company_logo");
CWizardUtil::CopyFile($companyLogo, BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/images/logo.gif", false);
CWizardUtil::CopyFile($companyLogo, BX_PERSONAL_ROOT."/templates/main_".WIZARD_SITE_ID."/images/logo.gif", false);
COption::SetOptionString("main", "wizard_site_logo", $companyLogo, false, WIZARD_SITE_ID);
?>