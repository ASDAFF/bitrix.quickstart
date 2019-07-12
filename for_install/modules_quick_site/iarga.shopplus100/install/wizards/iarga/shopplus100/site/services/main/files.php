<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");

if(COption::GetOptionString("iarga.shopplus100", "wizard_installed", "N") == 'N'){
	$file = fopen(WIZARD_SITE_ROOT_PATH."/bitrix/php_interface/init.php", "ab");
	fwrite($file, file_get_contents(WIZARD_ABSOLUTE_PATH."/site/services/main/bitrix/init.php"));
	fclose($file);
	COption::SetOptionString("iarga.shopplus100", "wizard_installed", "Y");
}
	
if(COption::GetOptionString("iarga.shopplus100", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();
	
	if($wizard->GetVar('siteLogoSet', true)){
		$ff = CFile::GetByID($wizard->GetVar("siteLogo"));	
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."inc/parts/logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'inc/parts/logo.jpg"  /></a>');
			CFile::Delete($siteLogo);
		}else if (!file_exists(WIZARD_SITE_PATH."inc/parts/logo.jpg")){
			copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.jpg", WIZARD_SITE_PATH."inc/parts/bx_default_logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'inc/parts/bx_default_logo.jpg"  /></a>');
			
		}
	}
	if($wizard->GetVar('siteNameSet', true)){
		___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_name.php", $wizard->GetVar("siteName"));
		if(file_exists(WIZARD_SITE_PATH."inc/parts/bx_default_logo.jpg"))
		{
			___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php",  '');
			unlink(WIZARD_SITE_PATH."inc/parts/bx_default_logo.jpg");
		}
	}
	___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/copyright.php", $wizard->GetVar("siteCopy"));
	___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/schedule.php", $wizard->GetVar("siteSchedule"));
	___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/telephone.php", $wizard->GetVar("siteTelephone"));

	if($wizard->GetVar('rewriteIndex', true)){
		if($wizard->GetVar('siteLogoSet', true)){
			CopyDirFiles(
				WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/_index_.php",
				WIZARD_SITE_PATH."/_index.php",
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = false
			);
		} else {
			CopyDirFiles(
				WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/_index.php",
				WIZARD_SITE_PATH."/_index.php",
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = false
			);
		}
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	}
	//die;
	return;
}

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"); 

$handle = @opendir($path);
if ($handle)
{
	while ($file = readdir($handle))
	{
		if (in_array($file, array(".", "..")))
			continue; 
/*			elseif (
			is_file($path.$file) 
			&& 
			(
				($file == "index.php"  && trim(WIZARD_SITE_PATH, " /") == trim(WIZARD_SITE_ROOT_PATH, " /"))
				|| 
				($file == "_index.php" && trim(WIZARD_SITE_PATH, " /") != trim(WIZARD_SITE_ROOT_PATH, " /"))
			)
		)
			continue; 
*/
		CopyDirFiles(
			$path.$file,
			WIZARD_SITE_PATH."/".$file,
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false,
			$exclude = "bitrix"
		);
		if($wizard->GetVar('siteLogoSet', true)){
			CopyDirFiles(
				WIZARD_SITE_PATH."/_index_.php",
				WIZARD_SITE_PATH."/_index.php",
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = true
			);
		}
		else
		{
			DeleteDirFilesEx(WIZARD_SITE_DIR."/_index_.php");
		}
	}
	

}

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."inc/", Array("SITE_DIR" => WIZARD_SITE_DIR));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."information/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."information/index.php",
		), 

	); 	
$arNewUrlRewrite[] =
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/index.php",
		);	
$arNewUrlRewrite[] =
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."basket/success/#",
		"RULE"	=>	"ORDER_ID=$1&",
		"ID"	=>	"",
		"PATH"	=>	 WIZARD_SITE_DIR."basket/success/index.php",
		);



foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
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

CheckDirPath(WIZARD_SITE_PATH."inc/parts/");

$wizard =& $this->GetWizard();

/*if($wizard->GetVar('siteLogoSet', true)){
	___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php", $wizard->GetVar("siteLogo"));
}*/
if($wizard->GetVar('siteNameSet', true)){
	___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_name.php", $wizard->GetVar("siteName"));
}
___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/copyright.php", $wizard->GetVar("siteCopy"));
___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/schedule.php", $wizard->GetVar("siteSchedule"));
___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/telephone.php", $wizard->GetVar("siteTelephone"));

if($wizard->GetVar('siteLogoSet', true)){
	$siteLogo = $wizard->GetVar("siteLogo");
	if($siteLogo>0)
	{
		$ff = CFile::GetByID($siteLogo);
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."inc/parts/logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'inc/parts/logo.jpg"  /></a>');
			CFile::Delete($siteLogo);
		}
	}
	else
	{
		copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.jpg", WIZARD_SITE_PATH."inc/parts/bx_default_logo.jpg");
		___writeToAreasFile(WIZARD_SITE_PATH."inc/parts/company_logo.php",  '<a href="'.WIZARD_SITE_DIR.'">'.$wizard->GetVar("siteName").'</a>');
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));



WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/auth/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/inc/parts/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/inc/ajax/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/basket/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/favorite/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/information/", Array("SITE_DIR" => WIZARD_SITE_DIR));

?>