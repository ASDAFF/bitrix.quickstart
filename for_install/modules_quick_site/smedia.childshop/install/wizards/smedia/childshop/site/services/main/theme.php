<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$templateDir = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID;

CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH,
	$_SERVER["DOCUMENT_ROOT"].$templateDir,
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);
if (file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR.'images/logo-bg.png'))
	copy (WIZARD_SITE_PATH.WIZARD_SITE_DIR.'images/logo-bg.png',WIZARD_SITE_PATH."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/images/logo-bg.png");
COption::SetOptionString("main", "wizard_".WIZARD_TEMPLATE_ID.'_'.WIZARD_SITE_ID."_theme_id", WIZARD_THEME_ID,'', WIZARD_SITE_ID);
?>