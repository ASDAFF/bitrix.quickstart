<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

DeleteDirFilesEx(WIZARD_SITE_PATH."/catalog");


CopyDirFiles(
			WIZARD_ABSOLUTE_PATH."/site/services/main/bitrix/components/mcart/",
			WIZARD_SITE_ROOT_PATH."/bitrix/components/mcart/",
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false
			
		);
CopyDirFiles(
			WIZARD_ABSOLUTE_PATH."/site/services/main/bitrix/php_interface/",
			WIZARD_SITE_ROOT_PATH."/bitrix/php_interface/",
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false
			
		);		

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

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SALE_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/delivery/", Array("SALE_PHONE" => $wizard->GetVar("siteTelephone")));

copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."news/index.php",
		), 
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."discount_coupon/#",
		"RULE"	=>	"",
		"ID"	=>	"mcart:iblock.element.add.form",
		"PATH"	=>	WIZARD_SITE_DIR."discount_coupon/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:photo",
		"PATH"	=>	WIZARD_SITE_DIR."catalog/index.php",
	),

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

CheckDirPath(WIZARD_SITE_PATH."include/");

$wizard =& $this->GetWizard();

/*if($wizard->GetVar('siteLogoSet', true)){
	___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", $wizard->GetVar("siteLogo"));
}*/
if($wizard->GetVar('siteNameSet', true)){
	___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", $wizard->GetVar("siteName"));
}
___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopy"));
___writeToAreasFile(WIZARD_SITE_PATH."include/schedule.php", $wizard->GetVar("siteSchedule"));
___writeToAreasFile(WIZARD_SITE_PATH."include/telephone.php", $wizard->GetVar("siteTelephone"));

if($wizard->GetVar('siteLogoSet', true)){
	$siteLogo = $wizard->GetVar("siteLogo");
	if($siteLogo>0)
	{
		$ff = CFile::GetByID($siteLogo);
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."include/logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'include/logo.jpg"  /></a>');
			CFile::Delete($siteLogo);
		}
	}
	else if(!file_exists(WIZARD_SITE_PATH."include/bx_default_logo.jpg") || WIZARD_INSTALL_DEMO_DATA)
	{
		copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.jpg", WIZARD_SITE_PATH."include/bx_default_logo.jpg");
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php",  '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'include/bx_default_logo.jpg"  /></a>');
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialchars($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialchars($wizard->GetVar("siteMetaKeywords"))));


?>