<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!defined("WIZARD_TEMPLATE_ID")) return;
$wizard =& $this->GetWizard();

$templateDir = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID;
CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH,
	$_SERVER["DOCUMENT_ROOT"].$templateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

COption::SetOptionString("main", "wizard_site_logo", WIZARD_SITE_LOGO, "", WIZARD_SITE_ID);
COption::SetOptionString("main", "wizard_".WIZARD_TEMPLATE_ID."_theme_id", WIZARD_THEME_ID, "", WIZARD_SITE_ID);
//Color scheme for main.interface.grid/form
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
CUserOptions::SetOption("main.interface", "global", array("theme" => WIZARD_THEME_ID), true);

$siteLogo = $wizard->GetVar("siteLogo");
if($siteLogo > 0)
{
    $file = CFile::GetByID($siteLogo);
    if($zr = $file->Fetch())
    {
        $strOldFile = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
        if(file_exists($strOldFile))
        {
            @unlink(WIZARD_SITE_PATH."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/images/logo.png");
            @copy($strOldFile, $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/images/logo.".end(explode(".", $zr["FILE_NAME"])));
            CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("EXT" => end(explode(".", $zr["FILE_NAME"]))));
            CFile::Delete($siteLogo);
        }
    }
}
else
{
    CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("EXT" => "png"));
}
?>