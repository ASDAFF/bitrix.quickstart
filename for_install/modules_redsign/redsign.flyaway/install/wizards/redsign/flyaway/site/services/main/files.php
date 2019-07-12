<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID") || !defined("WIZARD_SITE_DIR"))
	return;

WizardServices::IncludeServiceLang("files.php", $lang);
CModule::IncludeModule('files');

function ___writeToAreasFile($path, $text)
{
	//if(file_exists($fn) && !is_writable($abs_path) && defined("BX_FILE_PERMISSIONS"))
	//	@chmod($abs_path, BX_FILE_PERMISSIONS);

	$fd = @fopen($path, "wb");
	if(!$fd)
		return false;

	if(false === fwrite($fd, $text))
	{
		fclose($fd);
		return false;
	}

	fclose($fd);

	if(defined("BX_FILE_PERMISSIONS"))
		@chmod($path, BX_FILE_PERMISSIONS);
}

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");

if(COption::GetOptionString("redsign.flyaway", "wizard_installed", "N", WIZARD_SITE_ID) == "N" || WIZARD_INSTALL_DEMO_DATA)
{
	if(file_exists(WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"))
	{
		CopyDirFiles(
			WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/",
			WIZARD_SITE_PATH,
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = false
		);
	}
	COption::SetOptionString("redsign.flyaway", "template_converted", "Y", "", WIZARD_SITE_ID);
}
elseif (COption::GetOptionString("redsign.flyaway", "template_converted", "N", WIZARD_SITE_ID) == "N")
{
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/services/main/".LANGUAGE_ID."/public_convert/",
		WIZARD_SITE_PATH,
		$rewrite = true,
		$recursive = true,
		$delete_after_copy = false
	);
	CopyDirFiles(
		WIZARD_SITE_PATH."/include/company_logo.php",
		WIZARD_SITE_PATH."/include/company_logo_old.php",
		$rewrite = true,
		$recursive = true,
		$delete_after_copy = true
	);
	COption::SetOptionString("redsign.flyaway", "template_converted", "Y", "", WIZARD_SITE_ID);
}

$wizard =& $this->GetWizard();
___writeToAreasFile(WIZARD_SITE_PATH."include/footer/law.php", $wizard->GetVar("siteCopy"));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/header/phones.php', array('CONTACT_EMAIL' => $wizard->GetVar("shopEmail")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/header/phones.php', array('PHONE' => $wizard->GetVar("siteTelephone")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/footer/phones.php', array('PHONE' => $wizard->GetVar("siteTelephone")));

/*
if ($wizard->GetVar("templateID") != "al")
{
	$arSocNets = array("shopFacebook" => "facebook", "shopTwitter" => "twitter", "shopVk" => "vk", "shopGooglePlus" => "google");
	foreach($arSocNets as $socNet=>$includeFile)
	{
		$curSocnet = $wizard->GetVar($socNet);
		if ($curSocnet)
		{
			$text = '<a href="'.$curSocnet.'"></a>';
			___writeToAreasFile(WIZARD_SITE_PATH."include/socnet_".$includeFile.".php", $text);
		}
	}
}
*/
if(COption::GetOptionString("redsign.flyaway", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

// #SITE_DIR#
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."action/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."auth/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."brands/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."cities/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."delivery/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."forms/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include_areas/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."info/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."news/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."payment/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."projects/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."search/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."services/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."suppliers/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."404.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".bottom.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".catalog.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top_menu.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

// #SHOP_EMAIL#
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/header/phones.php', array('CONTACT_EMAIL' => $wizard->GetVar("shopEmail")));

// #SALE_PHONE#
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/header/phones.php', array('PHONE' => $wizard->GetVar("siteTelephone")));

// #SITE_SCHEDULE#
//WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", array("SITE_SCHEDULE" => $wizard->GetVar("siteSchedule")));
//WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."payment/", array("SITE_SCHEDULE" => $wizard->GetVar("siteSchedule")));

// #SITE_SMALL_ADDRESS#
$smallAdress = $wizard->GetVar("shopLocation").', '.$wizard->GetVar("shopAdr");
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/include/header/phones.php', array('SITE_SMALL_ADDRESS' => $smallAdress));

// SITE META
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));

// #REDSIGN_COPYRIGHTS#
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."/footer.php", array('REDSIGN_COPYRIGHT' => GetMessage('REDSIGN_COPYRIGHTS')));

    
$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."about/press_center/press_about/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."about/press_center/press_about/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."bitrix/services/ymarket/#",
		"RULE" => "",
		"ID" => "",
		"PATH" => WIZARD_SITE_DIR."bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."about/press_center/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."about/press_center/news/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."info/interesnye-stati/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."info/interesnye-stati/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."projects/perspective/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."projects/perspective/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."services/3d-design/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."services/3d-design/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."projects/finished/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."projects/finished/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."about/fotogallery/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."about/fotogallery/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."personal/delivery/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.profile",
		"PATH" => WIZARD_SITE_DIR."personal/delivery/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."projects/current/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."projects/current/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."personal/order/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => WIZARD_SITE_DIR."personal/order/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."about/partners/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."about/partners/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."about/history/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."about/history/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."services/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."brands/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."brands/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."catalog/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => WIZARD_SITE_DIR."catalog/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."action/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."action/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."news/index.php",
	),
);

foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}
