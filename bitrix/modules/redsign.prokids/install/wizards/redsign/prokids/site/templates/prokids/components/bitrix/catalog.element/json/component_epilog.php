<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

global $APPLICATION,$JSON;
$JSON = $templateData['JSON_EXT'];