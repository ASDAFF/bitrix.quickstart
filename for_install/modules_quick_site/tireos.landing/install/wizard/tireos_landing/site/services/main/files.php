<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID") || !defined("WIZARD_SITE_DIR"))
	return;

CModule::IncludeModule('iblock');

// Upload main files
if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");

if(COption::GetOptionString("tireos_start", "wizard_installed", "N", WIZARD_SITE_ID) == "N" || WIZARD_INSTALL_DEMO_DATA)
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
}

$wizard =& $this->GetWizard();

if(COption::GetOptionString("tireos_start", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."ajax/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."content/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."examples/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."images/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."search/", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."404.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

/*WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SALE_EMAIL" => $wizard->GetVar("shopEmail")));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/delivery/", Array("SALE_PHONE" => $wizard->GetVar("siteTelephone")));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));

copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");*/

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."examples/my-components/news/#",
		"RULE" => "",
		"ID" => "demo:news",
		"PATH" => WIZARD_SITE_DIR."examples/my-components/news_sef.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."content/articles/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."content/articles/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."content/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."content/news/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."content/faq/#",
		"RULE" => "",
		"ID" => "bitrix:support.faq",
		"PATH" => WIZARD_SITE_DIR."content/faq/index.php",
	),

);

foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}

// Upload init.php
if(file_exists(WIZARD_ABSOLUTE_PATH."/site/init/"))
{
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/init/",
		$_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/".WIZARD_SITE_ID."/", //WIZARD_SITE_PATH,
		$rewrite = true,
		$recursive = true,
		$delete_after_copy = false
	);
}

// Upload template
$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID;

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);

//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if ($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		if(!$found && strlen(trim($arTemplate["CONDITION"]))<=0)
		{
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID;
			$found = true;
		}
		if($arTemplate["TEMPLATE"] == "empty")
		{
			$foundEmpty = true;
			continue;
		}
		$arTemplates[]= $arTemplate;
	}

	if (!$found)
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID);

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}

$wizrdTemplateId = $wizard->GetVar("wizTemplateID");
COption::SetOptionString("main", "wizard_template_id", $wizrdTemplateId, false, WIZARD_SITE_ID);

// Install test data
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

// Create new Iblock type
$iblocktype = "tireos_landing_content";
$obIBlockType =  new CIBlockType;
$arFields = Array(
   "ID"=>$iblocktype,
   "SECTIONS"=>"Y",
   "LANG"=>Array(
      "ru"=>Array(
         "NAME"=>"[".WIZARD_SITE_ID."]".GetMessage("WIZ_CONTENT"),               
      )   
   )
);
$res = $obIBlockType->Add($arFields);



$data_dir = $_SERVER["DOCUMENT_ROOT"].WIZARD_RELATIVE_PATH."/site/data";

$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_ADVANTAGES.xml", "landing.advantages", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_ADVANTAGES", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_COMMENTS.xml", "landing.comments", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_COMMENTS", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_GALLERY.xml", "landing.gallery", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_GALLERY", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_INFO.xml", "landing.info", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_INFO", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_PARTNERS.xml", "landing.partners", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_PARTNERS", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_SLIDER.xml", "landing.slider", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_SLIDER", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_SPECIALS.xml", "landing.specials", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_SPECIALS", $ibID);
$ibID = WizardServices::ImportIBlockFromXML($data_dir."/EXPORT_TOPMENU.xml", "landing.topmenu", $iblocktype, WIZARD_SITE_ID);
if($ibID) COption::SetOptionString("tireos.landing", "IB_TOPMENU", $ibID);

?>