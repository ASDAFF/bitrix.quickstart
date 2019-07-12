<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

if (!CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('redsign.flyaway')
	|| !CModule::IncludeModule('redsign.devfunc')) {
	return;
}

$arTemplateParameters = array(
	'RSFLYAWAY_BLOCK_NAME' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.BLOCK_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'RSFLYAWAY_BLOCK_LINK' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.BLOCK_LINK'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
);

RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('blockName','owlSupport'));
if( $arCurrentValues['RSFLYAWAY_USE_OWL']=='Y' ) {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('owlSettings'));
} else {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('bootstrapCols'));
}