<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;
 
if (WIZARD_INSTALL_DEMO_DATA)
{
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
				$delete_after_copy = false
			);
		}
		CModule::IncludeModule("search");
		CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
	}

	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

	$arUrlRewrite = array(); 
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}

	$arNewUrlRewrite = array(
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."diagnostic/(.+?)/.*$#",
			"RULE"	=>	"SECTION_CODE=$1",
			"PATH"	=>	WIZARD_SITE_DIR."diagnostic/section.php",
		),
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."doctors/(.+?)/.*$#",
			"RULE"	=>	"SECTION_CODE=$1",
			"PATH"	=>	WIZARD_SITE_DIR."doctors/section.php",
		),
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/(.+?)/.*$#",
			"RULE"	=>	"ID=$1",
			"PATH"	=>	WIZARD_SITE_DIR."news/detail.php",
		),
	); 
	
	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
	}
}

function ___writeToAreasFile($fn, $text)
{
	if(file_exists($fn) && !is_writable($abs_path) && defined("BX_FILE_PERMISSIONS"))
		@chmod($abs_path, BX_FILE_PERMISSIONS);

	$fd = @fopen($fn, "wb");
	if(!$fd)
		return false;

	if(false === fwrite($fd, $text))
	{
		fclose($fd);
		return false;
	}

	fclose($fd);

	if(defined("BX_FILE_PERMISSIONS"))
		@chmod($fn, BX_FILE_PERMISSIONS);
}


CheckDirPath(WIZARD_SITE_PATH."include/");

$wizard =& $this->GetWizard();
___writeToAreasFile(WIZARD_SITE_PATH."include/clinic_name.php", $wizard->GetVar("siteClinicNameText"));
___writeToAreasFile(WIZARD_SITE_PATH."include/address.php", $wizard->GetVar("siteAddressText"));
___writeToAreasFile(WIZARD_SITE_PATH."include/phone.php", $wizard->GetVar("sitePhoneText"));
___writeToAreasFile(WIZARD_SITE_PATH."include/worktime.php", $wizard->GetVar("siteWorktimeText"));
___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopyrightText"));
___writeToAreasFile(WIZARD_SITE_PATH."include/banner.php", $wizard->GetVar("siteBannerText"));

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

/*
$siteLogo = $wizard->GetVar("siteLogo");
if($siteLogo>0)
{
	$ff = CFile::GetByID($siteLogo);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		@copy($strOldFile, WIZARD_SITE_PATH."include/logo.gif");
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.gif"  />');
		CFile::Delete($siteLogo);
	}
}
elseif(!file_exists(WIZARD_SITE_PATH."include/company_name.php"))
{
	copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.gif", WIZARD_SITE_PATH."include/logo.gif");
	___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.gif"  />');
}

$siteBanner = $wizard->GetVar("siteBanner");
if($siteBanner>0)
{
	$ff = CFile::GetByID($siteBanner);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		@copy($strOldFile, WIZARD_SITE_PATH."include/banner.".GetFileExtension($zr["FILE_NAME"]));
		___writeToAreasFile(WIZARD_SITE_PATH."include/banner.php", '<img src="'.WIZARD_SITE_DIR.'include/banner.'.GetFileExtension($zr["FILE_NAME"]).'"  />');
		CFile::Delete($siteBanner);
	}
}
elseif(!file_exists(WIZARD_SITE_PATH."include/banner.php"))
{
	copy(WIZARD_TEMPLATE_ABSOLUTE_PATH."/images/banner.png", WIZARD_SITE_PATH."include/banner.png");
	___writeToAreasFile(WIZARD_SITE_PATH."include/banner.php", '<img src="'.WIZARD_SITE_DIR.'include/banner.png"  />');
}
*/

?>