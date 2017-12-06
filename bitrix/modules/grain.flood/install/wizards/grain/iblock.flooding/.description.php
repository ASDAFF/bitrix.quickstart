<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arWizardDescription = Array(
	"NAME" => Loc::getMessage('GRAIN_FLOOD_IBF_DESCR_TITLE'), 
	"DESCRIPTION" => Loc::getMessage('GRAIN_FLOOD_IBF_DESCR_DESCRIPTION'), 
	"ICON" => "",
	"COPYRIGHT" => Loc::getMessage('GRAIN_FLOOD_IBF_DESCR_COPY'),
	"VERSION" => "1.0.0",
	"STEPS" => Array("Step1", "Step2", "Step3", "FinalStep", "CancelStep"),
);

?>