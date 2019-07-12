<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if(COption::GetOptionString("fashion", "wizard_installed", "N") == 'N'){
	COption::SetOptionString("fashion", "wizard_installed", "Y");
}

if(COption::GetOptionString("fashion", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();

	if($wizard->GetVar('siteLogoSet', true)){
		$ff = CFile::GetByID($wizard->GetVar("siteLogo"));
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."include/logo.png");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.png" class="logo fn org" />');
			CFile::Delete($siteLogo);
		}else if (!file_exists(WIZARD_SITE_PATH."include/logo.png")){
			copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/ru/logo.png", WIZARD_SITE_PATH."include/default_logo.png");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<img src="'.WIZARD_SITE_DIR.'include/default_logo.png" class="logo fn org" />');

		}
	}
	/*
	if($wizard->GetVar('siteNameSet', true)){
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", $wizard->GetVar("siteName"));
		if(file_exists(WIZARD_SITE_PATH."include/default_logo.png"))
		{
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php",  '');
			unlink(WIZARD_SITE_PATH."include/default_logo.png");
		}
	}*/

	___writeToAreasFile(WIZARD_SITE_PATH."include/schedule.php", $wizard->GetVar("siteSchedule"));
	___writeToAreasFile(WIZARD_SITE_PATH."include/telephone.php", $wizard->GetVar("siteTelephone"));
	___writeToAreasFile(WIZARD_SITE_PATH."include/locality.php", $wizard->GetVar("shopLocation"));
	___writeToAreasFile(WIZARD_SITE_PATH."include/street_address.php", $wizard->GetVar("shopAdr"));

	if($wizard->GetVar('rewriteIndex', true)){
		CopyDirFiles(
			WIZARD_ABSOLUTE_PATH."/site/public/ru/_index.php",
			WIZARD_SITE_PATH."/_index.php",
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = false
		);

		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	}
	//die;
	return;
}

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/ru/");

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
/*
		if($wizard->GetVar('siteLogoSet', true)){
			CopyDirFiles(
				WIZARD_SITE_PATH."/_index_.php",
				WIZARD_SITE_PATH."/_index.php",
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = true
			);
		}
*/
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

$arNewUrlRewrite = array();
$arNewUrlRewrite[] = array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/index.php",
	);

$arNewUrlRewrite[] = array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."personal/order/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:sale.personal.order",
		"PATH"	=>	 WIZARD_SITE_DIR."personal/order/index.php",
	);
	
$arNewUrlRewrite[] = array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."brands/#",
		"RULE"	=>	"",
		"ID"	=>	"fashion:catalog",
		"PATH"	=>	 WIZARD_SITE_DIR."brands/index.php",
	);
	
$arNewUrlRewrite[] = array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."wishlist/(.+)/.*#",
		"RULE"	=>	"LOGIN=$1",
		"PATH"	=>	 WIZARD_SITE_DIR."wishlist/index.php",
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
//___writeToAreasFile(WIZARD_SITE_PATH."include/company_slogan.php", $wizard->GetVar("siteSlogan"));
//___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopy"));
___writeToAreasFile(WIZARD_SITE_PATH."include/schedule.php", $wizard->GetVar("siteSchedule"));
___writeToAreasFile(WIZARD_SITE_PATH."include/telephone.php", $wizard->GetVar("siteTelephone"));
//___writeToAreasFile(WIZARD_SITE_PATH."include/time.php", $wizard->GetVar("siteSchedule"));
//___writeToAreasFile(WIZARD_SITE_PATH."include/ads.php", $wizard->GetVar("siteAdsCommon"));
//___writeToAreasFile(WIZARD_SITE_PATH."include/trunks.php", $wizard->GetVar("siteAdsMount"));
//___writeToAreasFile(WIZARD_SITE_PATH."include/address.php", $wizard->GetVar("shopLocation").', '.$wizard->GetVar("shopAdr"));

if($wizard->GetVar('siteLogoSet', true)){
	$siteLogo = $wizard->GetVar("siteLogo");
	if($siteLogo>0)
	{
		$ff = CFile::GetByID($siteLogo);
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."include/logo.png");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.png" class="logo fn org" />');
			CFile::Delete($siteLogo);
		}
	}
	else if(!file_exists(WIZARD_SITE_PATH."include/default_logo.jpg") || WIZARD_INSTALL_DEMO_DATA)
	{
		copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.png", WIZARD_SITE_PATH."include/default_logo.png");
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php",  '<img src="'.WIZARD_SITE_DIR.'include/default_logo.png" class="logo fn org" />');
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => $wizard->GetVar("siteMetaDescription")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => $wizard->GetVar("siteMetaKeywords")));
?>
