<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$templateDir = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
$templateDirPromo = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_index_".WIZARD_THEME_ID;

CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH,
	$_SERVER["DOCUMENT_ROOT"].$templateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

//themes for promo
CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID."_index/themes/".WIZARD_THEME_ID,
	$_SERVER["DOCUMENT_ROOT"].$templateDirPromo,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

COption::SetOptionString("main", "wizard_fashion_theme_id", WIZARD_THEME_ID, "", WIZARD_SITE_ID);

//Color scheme for main.interface.grid/form
//require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
//CUserOptions::SetOption("main.interface", "global", array("theme" => WIZARD_THEME_ID), true);
?>
