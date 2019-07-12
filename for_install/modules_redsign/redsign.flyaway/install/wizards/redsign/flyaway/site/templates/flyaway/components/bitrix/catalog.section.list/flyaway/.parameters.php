<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('redsign.flyaway')
	|| !CModule::IncludeModule('redsign.devfunc')) {
	return;
}

$arTemplateParameters = array();