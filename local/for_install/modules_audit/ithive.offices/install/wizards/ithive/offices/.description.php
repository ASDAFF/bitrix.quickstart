<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" => GetMessage("OFFICES_WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("OFFICES_WIZARD_DESC"), 
	"VERSION" => "1.0.0",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "/images/solution.png",
	"TEMPLATES" => Array(
		Array("SCRIPT" => "scripts/template.php", "CLASS" => "WizardTemplate")
	),
	"STEPS" => (defined("WIZARD_DEFAULT_SITE_ID") ? 
		Array("SelectIblockType", "TypeNewIblockName", "SelectPathToInstallComponent", "DataInstallStep" ,"FinishStep") : 
		Array("SelectSiteStep", "SelectIblockType", "TypeNewIblockName", "SelectPathToInstallComponent", "DataInstallStep" ,"FinishStep"))		
);
?>
