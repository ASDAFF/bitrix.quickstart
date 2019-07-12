<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" => GetMessage("REDSIGN.OPTPRO.WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("REDSIGN.OPTPRO.WIZARD_DESC"), 
	"VERSION" => "1.0.0",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "/images/".LANGUAGE_ID."/solution.png",
	"PARENT" => "wizard_sol",
	"TEMPLATES" => Array(
		Array("SCRIPT" => "wizard_sol")
	),
	"STEPS" => Array(
		"SelectTemplateStep",
		"SelectThemeStep",
		"SiteSettingsStep",
		"ShopSettings",
		"PersonType",
		"PaySystem",
		"DataInstallStep",
		"FinishStep",
	),
);
if(defined("WIZARD_DEFAULT_SITE_ID"))
{
	if(LANGUAGE_ID == "ru")
	{
		$arWizardDescription["STEPS"] = Array("SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	} else {
		$arWizardDescription["STEPS"] = Array("SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	}
} else {
	if(LANGUAGE_ID == "ru")
	{
		$arWizardDescription["STEPS"] = Array("SelectSiteStep", "SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	} else {
		$arWizardDescription["STEPS"] = Array("SelectSiteStep", "SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	}
}