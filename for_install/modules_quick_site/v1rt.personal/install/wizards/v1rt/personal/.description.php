<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" => GetMessage("PORTAL_WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("PORTAL_WIZARD_DESC"), 
	"VERSION" => "2.1.7",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"PARENT" => "wizard_sol",
    "TEMPLATES" => Array(
        Array("SCRIPT" => "template/template.php", "CLASS" => "V1rtSiteWizardTemplate")
    ),
	"STEPS" => (defined("WIZARD_DEFAULT_SITE_ID") ? 
		Array("StartStep", "LicenseStep", "SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "DataInstallStep" ,"FinishStep") :
		Array("StartStep", "LicenseStep", "SelectSiteStep", "SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "DataInstallStep" ,"FinishStep"))
);
?>