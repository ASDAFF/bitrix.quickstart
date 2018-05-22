<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	'BRAND_PAGE' => array(
		'NAME' => GetMessage('BRAND_PAGE'),
		'TYPE' => 'STRING',
	),
	'ADD_STYLES_FOR_MAIN' => array(
		'NAME' => GetMessage('ADD_STYLES_FOR_MAIN'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
);