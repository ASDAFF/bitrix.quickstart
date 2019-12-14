<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	'FOOTER_MENU_TITLE' => array(
		'NAME' => GetMessage('FOOTER_MENU_TITLE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => GetMessage('FOOTER_MENU_TITLE_EXAMPLE'),
	)
);