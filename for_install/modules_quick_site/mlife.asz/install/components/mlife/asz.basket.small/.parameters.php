<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		
	),
);
?>