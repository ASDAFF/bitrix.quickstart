<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$templateDir = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID;

CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH,
	$_SERVER["DOCUMENT_ROOT"].$templateDir,
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "description.php"
);

 if (WIZARD_SITE_LOGO > 0){
	$success = CWizardUtil::CopyFile(WIZARD_SITE_LOGO, $templateDir."/images/logo.png", false);
	
	if ($handle = @fopen($_SERVER['DOCUMENT_ROOT']."/include_areas/logo.php", "w")) {
                        $file_string='<img src="'.$templateDir.'/images/logo.png" alt="">';

                                @fwrite($handle,$file_string);
                                @fclose($handle);
                }
				
	}

COption::SetOptionString("main", "wizard_site_logo", WIZARD_SITE_LOGO);
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/script.js", Array("SITE_DIR" => WIZARD_SITE_DIR));
// COption::SetOptionString("main", "wizard_".WIZARD_TEMPLATE_ID."_theme_id", WIZARD_THEME_ID);

// ѕытаемс€ поставить тему форума по теме сайта
?>