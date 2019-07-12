<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.flyaway'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	'RSFLYAWAY_SHOW_BUTTON' => array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_BUTTON'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
);

if( $arCurrentValues['RSFLYAWAY_SHOW_BUTTON']=='Y' ) {
	$arTemplateParameters['RSFLYAWAY_BUTTON_CAPTION'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.BUTTON_CAPTION'),
		'TYPE' => 'STRING',
	);
}
