<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");
	
if(COption::GetOptionString("minilanding", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();
	return;
}

//копирование файлов
$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"); 
$handle = @opendir($path);
if ($handle)
{
	while ($file = readdir($handle))
	{
		if (in_array($file, array(".", ".."))) 
			continue;
		CopyDirFiles(
			$path.$file,
			WIZARD_SITE_PATH."/".$file,
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false,
			$exclude = "bitrix"
		);

			DeleteDirFilesEx(WIZARD_SITE_DIR."/_index_.php");
		
	}
}

//макросы
WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

//почтовые шаблоны
$e_id1 = COption::GetOptionString("mlife.minilanding","event1");
$e_id2 = COption::GetOptionString("mlife.minilanding","event2");
$e_id3 = COption::GetOptionString("mlife.minilanding","event3");

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/logo.php", array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_TEXT2" => $wizard->GetVar("siteTextBottom"), "SITE_TEXT1" => $wizard->GetVar("siteTextTop")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/logo2.php", array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_TEXT2" => $wizard->GetVar("siteTextBottom"), "SITE_TEXT1" => $wizard->GetVar("siteTextTop")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/logo3.php", array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_TEXT2" => $wizard->GetVar("siteTextBottom"), "SITE_TEXT1" => $wizard->GetVar("siteTextTop")));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include_areas/phone.php", Array( "SITE_PHONE" => $wizard->GetVar("sitePhone")));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_EMAIL" => $wizard->GetVar("siteEmail"), "EVENT1" => $e_id1, "EVENT2" => $e_id2, "EVENT3" => $e_id3, "EVENT4" => $e_id4, "SITE_TEXT1" => $wizard->GetVar("siteTextTop")));
																
copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");

$wizard =& $this->GetWizard();