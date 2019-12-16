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

if(COption::GetOptionString("redsign.activelife", "wizard_installed", "N", WIZARD_SITE_ID) == "N" || WIZARD_INSTALL_DEMO_DATA)
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
	COption::SetOptionString("redsign.activelife", "template_converted", "Y", "", WIZARD_SITE_ID);
}
elseif (COption::GetOptionString("redsign.activelife", "template_converted", "N", WIZARD_SITE_ID) == "N")
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
	COption::SetOptionString("redsign.activelife", "template_converted", "Y", "", WIZARD_SITE_ID);
}

$wizard =& $this->GetWizard();
___writeToAreasFile(WIZARD_SITE_PATH."include/copyright.php", $wizard->GetVar("siteCopy"));
___writeToAreasFile(WIZARD_SITE_PATH."include/telephone1.php", $wizard->GetVar("siteTelephone"));
___writeToAreasFile(WIZARD_SITE_PATH."include/telephone2.php", $wizard->GetVar("siteTelephone2"));

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
if(COption::GetOptionString("redsign.activelife", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

// #SITE_DIR#
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".footer.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".personal.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".toppanel.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."action/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."brands/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."forms/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."kredit/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."news/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."sales/", Array("SITE_DIR" => WIZARD_SITE_DIR));


// #SITE_TEMPLATE_PATH#
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SITE_TEMPLATE_PATH" => BX_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID));

// #SHOP_EMAIL#
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."buy1click/index.php", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."feedback/index.php", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."recall/index.php", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."kredit/", Array("SHOP_EMAIL" => $wizard->GetVar("shopEmail")));

// #SALE_PHONE#
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", Array("SALE_PHONE" => $wizard->GetVar("siteTelephone").' '.$wizard->GetVar("siteTelephone2")));

// #SITE_SCHEDULE#
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", array("SITE_SCHEDULE" => $wizard->GetVar("siteSchedule")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."payment/", array("SITE_SCHEDULE" => $wizard->GetVar("siteSchedule")));

// #SITE_SMALL_ADDRESS#
$smallAdress = $wizard->GetVar("shopLocation").', '.$wizard->GetVar("shopAdr");
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."contacts/", array("SITE_SMALL_ADDRESS" => $smallAdress));

// SITE META
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));

// #REDSIGN_COPYRIGHT#
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."/footer.php", array('REDSIGN_COPYRIGHT' => GetMessage('REDSIGN_COPYRIGHT')));

    
$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."personal/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.section",
		"PATH" => WIZARD_SITE_DIR."personal/index.php",
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
?>