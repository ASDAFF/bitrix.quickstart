<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

if (!CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('catalog')
	|| !CModule::IncludeModule('redsign.devfunc')) {
	return;
}

$arTemplateParameters = array(
	'FOOTER_MENU_TITLE' => array(
		'NAME' => Loc::getMessage('FOOTER_MENU_TITLE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => Loc::getMessage('FOOTER_MENU_TITLE_EXAMPLE'),
	)
);