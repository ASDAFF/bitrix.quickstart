<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.flyaway'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	'RSFLYAWAY_SHOW_DATE' => array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_DATE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
);

RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('blockName','owlSupport'));
if( $arCurrentValues['RSFLYAWAY_USE_OWL']=='Y' ) {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('owlSettings'));
} else {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('bootstrapCols'));
}