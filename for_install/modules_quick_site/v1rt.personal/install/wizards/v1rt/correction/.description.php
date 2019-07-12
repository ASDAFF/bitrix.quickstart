<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arWizardDescription = array(
	"NAME" => GetMessage("V1RT_CORRECTION_WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("V1RT_CORRECTION_WIZARD_DESC"),
	"STEPS" => array("WizDescription", "WizCorrectionStep1", "WizCorrectionStep2", "WizFinish", "WizCancelStep")
);
?>