<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;

function ___writeToAreasFile($fn, $text){
	if(file_exists($fn) && !is_writable($abs_path) && defined("BX_FILE_PERMISSIONS")){
		@chmod($abs_path, BX_FILE_PERMISSIONS);
	}
	if(!$fd = @fopen($fn, "wb")){
		return false;
	}
	if(!$res = @fwrite($fd, $text)){
		@fclose($fd);
		return false;
	}
	@fclose($fd);
	if(defined("BX_FILE_PERMISSIONS"))
		@chmod($fn, BX_FILE_PERMISSIONS);
}

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";
$wizard =& $this->GetWizard();

if(COption::GetOptionString("main", "upload_dir") == ""){
	COption::SetOptionString("main", "upload_dir", "upload");
}

if(COption::GetOptionString("aspro.optimus", "wizard_installed", "N") == 'N'){
	// if need add to init.php
	//$file = fopen(WIZARD_SITE_ROOT_PATH."/bitrix/php_interface/init.php", "ab");
	//fwrite($file, file_get_contents(WIZARD_ABSOLUTE_PATH."/site/services/main/bitrix/init.php"));
	//fclose($file);
	COption::SetOptionString("aspro.optimus", "wizard_installed", "Y");
}


if(WIZARD_INSTALL_DEMO_DATA){
	// copy files
	CopyDirFiles(
		str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"),
		WIZARD_SITE_PATH,
		$rewrite = true, 
		$recursive = true,
		$delete_after_copy = false,
		$exclude = "bitrix"
	);

	// favicon
	//@copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");
	
	// .htaccess
	WizardServices::PatchHtaccess(WIZARD_SITE_PATH);
	
	// replace macros SITE_DIR & SITE_ID
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_ID" => WIZARD_SITE_ID));

	// add to UrlRewrite
	$arUrlRewrite = array(); 
	if(file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php")){
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}
	
	$arNewUrlRewrite = array(
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."bitrix/services/ymarket/([\\w\\d\\-]+)?(/)?(([\\w\\d\\-]+)(/)?)?#",
			"RULE" => "REQUEST_OBJECT=\$1&METHOD=\$4",
			"ID" => "",
			"PATH" => WIZARD_SITE_DIR."bitrix/services/ymarket/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."personal/history-of-orders/#",
			"RULE" => "",
			"ID" => "bitrix:sale.personal.order",
			"PATH" => WIZARD_SITE_DIR."personal/history-of-orders/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."contacts/stores/#",
			"RULE" => "",
			"ID" => "bitrix:catalog.store",
			"PATH" => WIZARD_SITE_DIR."contacts/stores/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."personal/order/#",
			"RULE" => "",
			"ID" => "bitrix:sale.personal.order",
			"PATH" => WIZARD_SITE_DIR."personal/order/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."info/articles/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."info/articles/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."company/news/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."company/news/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."info/article/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."info/article/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."info/brands/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."info/brands/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."info/brand/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."info/brand/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."products/#",
			"RULE" => "",
			"ID" => "bitrix:catalog",
			"PATH" => WIZARD_SITE_DIR."products/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."services/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."catalog/#",
			"RULE" => "",
			"ID" => "bitrix:catalog",
			"PATH" => WIZARD_SITE_DIR."catalog/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."sale/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."sale/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."news/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."news/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."personal/#",
			"RULE" => "",
			"ID" => "bitrix:sale.personal.section",
			"PATH" => WIZARD_SITE_DIR."personal/index.php",
		),
	);
	
	foreach($arNewUrlRewrite as $arUrl){
		if(!in_array($arUrl, $arUrlRewrite)){
			CUrlRewriter::Add($arUrl);
		}
	}
}

CheckDirPath(WIZARD_SITE_PATH."include/");

// site name
if($wizard->GetVar('siteNameSet', true)){
	$siteName = $wizard->GetVar("siteName");
	COption::SetOptionString("main", "site_name", $siteName);	
	$obSite = new CSite;
	$arFields = array("NAME" => $siteName, "SITE_NAME" => $siteName);			
	$siteRes = $obSite->Update(WIZARD_SITE_ID, $arFields);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_NAME" => $siteName));
}
// copyright
___writeToAreasFile(WIZARD_SITE_PATH."include/footer/copy/copyright.php", "<?=date(\"Y\")?> &copy; ".$wizard->GetVar("siteCopy"));
// phone
$sitePhone = $wizard->GetVar("siteTelephone");
$sitePhone2 = $wizard->GetVar("siteTelephone2");
$sitePhoneAll='<a href="tel:'.str_replace(" ", "", $sitePhone).'" rel="nofollow">'.$sitePhone.'</a>';
if($sitePhone2){	
	$sitePhoneAll.='<a href="tel:'.str_replace(" ", "", $sitePhone2).'" rel="nofollow">'.$sitePhone2.'</a>';
}
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/phone.php", Array("SITE_PHONE" => $sitePhoneAll));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/basket_print_desc.php", Array("SITE_PHONE" => $sitePhoneAll));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/jobs_description_phone.php", Array("SITE_PHONE" => $sitePhoneAll));
// email
$siteEmail = $wizard->GetVar("siteEmail");
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/email.php", Array("SITE_EMAIL" => $siteEmail));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/basket_print_desc.php", Array("SITE_EMAIL" => $siteEmail));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/jobs_description_phone.php", Array("SITE_EMAIL" => $siteEmail));
// skype
//$siteSkype = $wizard->GetVar("siteSkype");
//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/site-skype.php", Array("SITE_SKYPE" => $siteSkype));
// address
/*$siteAddress = $wizard->GetVar("siteAddress");
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/address.php", Array("SITE_ADDRESS" => $siteAddress));*/
$shopAdr = trim($wizard->GetVar("shopAdr"));
$shopLocation = trim($wizard->GetVar("shopLocation"));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/address.php", Array("SITE_ADDRESS" => $shopLocation.', '.$shopAdr));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/basket_print_desc.php", Array("SITE_ADDRESS" => $shopLocation.', '.$shopAdr));
// schedule
$siteSchedule = $wizard->GetVar("siteSchedule");
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/schedule.php", Array("SITE_SCHEDULE" => $siteSchedule));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."include/basket_print_desc.php", Array("SITE_SCHEDULE" => $siteSchedule));

// meta
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));
// logo
if($wizard->GetVar('siteLogoSet', true)){
	$templateID = $wizard->GetVar("templateID");
	$themeVarName = $templateID."_themeID";
	$themeID = $wizard->GetVar($themeVarName);
	$siteLogo = $wizard->GetVar("siteLogo");
	$ff = CFile::GetByID($siteLogo);
	if($zr = $ff->Fetch()){
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		@copy($strOldFile, WIZARD_SITE_PATH."include/logo.png");
		//___writeToAreasFile(WIZARD_SITE_PATH."include/logo.php", '<a href="'.WIZARD_SITE_DIR.'"><img src="'.WIZARD_SITE_DIR.'include/logo.png"  /></a>');
		CFile::Delete($siteLogo);
	}
}

// socials
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_VK" => $wizard->GetVar("shopVk")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_ODNOKLASSNIKI" => $wizard->GetVar("shopOdnoklassniki")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_FACEBOOK" => $wizard->GetVar("shopFacebook")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_TWITTER" => $wizard->GetVar("shopTwitter")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_MAILRU" => $wizard->GetVar("shopMailru")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_INSTAGRAM" => $wizard->GetVar("shopInstagram")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_YOUTUBE" => $wizard->GetVar("shopYouTube")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, array("SITE_GOOGLE" => $wizard->GetVar("shopGoogle")));

// rewrite /index.php
if($wizard->GetVar('rewriteIndex', true)){
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/_index.php",
		WIZARD_SITE_PATH."/index.php",
		$rewrite = true,
		$recursive = true,
		$delete_after_copy = false
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
}

DeleteDirFilesEx(WIZARD_SITE_PATH."/.bottom.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.bottom_company.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.bottom_help.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.bottom_info.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.bottom_main.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.left.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.top.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.top_general.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.type_1.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/.type_2.menu_ext.php");
DeleteDirFilesEx(WIZARD_SITE_PATH."/_index.php");
?>