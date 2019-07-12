<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");

if(COption::GetOptionString("softeffect.storesoftware", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();
	
	if($wizard->GetVar('siteLogoSet', true)){
		$ff = CFile::GetByID($wizard->GetVar("siteLogo"));	
		if($zr = $ff->Fetch())
		{
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."include/logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'include/logo.jpg"  /></a>');
			CFile::Delete($siteLogo);
		} elseif (!file_exists(WIZARD_SITE_PATH."include/logo.jpg")) {
			copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.jpg", WIZARD_SITE_PATH."include/bx_default_logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'include/bx_default_logo.jpg"  /></a>');
			
		}
	}

	if ($wizard->GetVar('siteNameSet', true)) {
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", $wizard->GetVar("siteName"));
	}
	___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopy"));
	___writeToAreasFile(WIZARD_SITE_PATH."include/telephone.php", $wizard->GetVar("siteTelephone"));

	if ($wizard->GetVar('rewriteIndex', true)) {
		CopyDirFiles(
			WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/_index.php",
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

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"); 

$handle = @opendir($path);
if ($handle) {
	while ($file = readdir($handle)) {
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
	}
}

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SALE_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SALE_PHONE" => $wizard->GetVar("siteTelephone")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SKYPE" => $wizard->GetVar("siteSkype")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("ICQ" => $wizard->GetVar("siteICQ")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SERVER_NAME" => $_SERVER['SERVER_NAME']));

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_NAME" => $wizard->GetVar("shopOfName")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_LOCATION" => $wizard->GetVar("shopLocation")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_ADR" => $wizard->GetVar("shopAdr")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_INN" => $wizard->GetVar("shopAdr")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_KPP" => $wizard->GetVar("shopINN")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_RS" => $wizard->GetVar("shopNS")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_BANK" => $wizard->GetVar("shopBANK")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_BANKREKV" => $wizard->GetVar("shopBANKREKV")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_KS" => $wizard->GetVar("shopKS")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_OKPO" => $wizard->GetVar("shopOKPO")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SHOP_OGRN" => $wizard->GetVar("shopOGRN")));

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."developments/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."developments/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."about/partners/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."about/partners/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."personal/order/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:sale.personal.order",
		"PATH"	=>	 WIZARD_SITE_DIR."personal/order/index.php",
	
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."about/reviews/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."about/reviews/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."blog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:blog",
		"PATH"	=>	 WIZARD_SITE_DIR."blog/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."news/index.php",
	)
);

$MYarUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/category/([A-Za-z0-9-_\.]+)/(.*)#",
		"RULE"	=>	"CATEGORY=$1",
		"ID"	=>	"",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/category.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/detail/([A-Za-z0-9-_\.]+)/(.*)#",
		"RULE"	=>	"ELEMENT=$1",
		"ID"	=>	"",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/detail.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/(?!category)(?!detail)([A-Za-z0-9-_\.]+)/([A-Za-z0-9-_\.]+)/(.*)#",
		"RULE"	=>	"SECTION=$1&SECTION_L2=$2",
		"ID"	=>	"",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/section.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/([A-Za-z0-9-_\.]+)/(.*)#",
		"RULE"	=>	"SECTION=$1",
		"ID"	=>	"",
		"PATH"	=>	 WIZARD_SITE_DIR."catalog/index.php",
	),
);

$arNewUrlRewrite = array_merge($MYarUrlRewrite, $arNewUrlRewrite);

foreach ($arNewUrlRewrite as $arUrl) {
	if (!in_array($arUrl, $arUrlRewrite)) {
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

if($wizard->GetVar('siteNameSet', true)){
	___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", $wizard->GetVar("siteName"));
}
___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopy"));
___writeToAreasFile(WIZARD_SITE_PATH."include/telephone.php", $wizard->GetVar("siteTelephone"));

if($wizard->GetVar('siteLogoSet', true)){
	$siteLogo = $wizard->GetVar("siteLogo");
	if($siteLogo>0) {
		$ff = CFile::GetByID($siteLogo);
		if($zr = $ff->Fetch()) {
			$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
			@copy($strOldFile, WIZARD_SITE_PATH."images/header_logo.jpg");
			___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'images/header_logo.jpg" /></a>');
			CFile::Delete($siteLogo);
		}
	} else if(!file_exists(WIZARD_SITE_PATH."include/bx_default_logo.jpg") || WIZARD_INSTALL_DEMO_DATA) {
		copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/header_logo.jpg", WIZARD_SITE_PATH."images/bx_default_logo.jpg");
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'images/bx_default_logo.jpg" /></a>');
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription")), "SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));
?>