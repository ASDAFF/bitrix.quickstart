<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) {
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 
}

$arWizardDescription = Array(
        "NAME" => GetMessage("SMChildShop_WIZARD_NAME"),
        "DESCRIPTION" => GetMessage("SMChildShop_WIZARD_DESC"),
        "VERSION" => "1.0.7",
        "START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "images/".LANGUAGE_ID."/solution.png",
"PARENT" => "wizard_sol",
        "TEMPLATES" => Array(
                Array("SCRIPT" => "scripts/template.php", "CLASS" => "WizardTemplate")
        ),
        
        "STEPS" => Array(
	    "WelcomeStep",          
	    "SelectTemplateStep",  
	    "SiteSettingsStep",   	  	  
	    "DataInstallStep",   
	    "FinishStep"          // This is the End (c) Beatles
	  ),   	
); 

?>