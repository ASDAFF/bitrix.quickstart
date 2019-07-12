<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arWizardDescription = Array(
	"NAME" => GetMessage('CATWIZ_DESCR_NAME'),
	"DESCRIPTION" => GetMessage('CATWIZ_DESCR_DESCRIPTION'),
	"ICON" => "",
	"COPYRIGHT" => GetMessage('CATWIZ_DESCR_COPYRIGHT'),
	"VERSION" => "1.0.0",
	"START_TYPE" => "WINDOW",
	"TEMPLATES" => Array(Array("SCRIPT" => "scripts/template.php", "CLASS" => "WizardTemplate")),
	"STEPS" => Array("StepDescription", "StepSettings", "StepProps", "StepRun", "FinalStep", "CancelStep"),
);

?>