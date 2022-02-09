<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arWizardDescription = Array(
	"NAME" => GetMessage("BEONO_MASTER_COMP_TITLE"), 
	"DESCRIPTION" => GetMessage("BEONO_MASTER_COMP_DESCR"), 
	"ICON" => "icon.png",
	"COPYRIGHT" => "Eldar Beono (ibeono@gmail.com)",
	"VERSION" => "1.0.3",
	"DEPENDENCIES" => Array(
	),
	"STEPS" => Array("WelcomeStep", "NameStep", "ModuleStep", "TemplateStep", "InstallStep", "FinalStep", "CancelStep"),

	/*"TEMPLATES" => Array(
		Array("SCRIPT" => "wizard_template.php", "CLASS" => "DemoSiteTemplate"),
	),*/
);

?>