<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	'RSMONOPOLY_BLOCK_NAME' => array(
		'NAME' => GetMessage('RS.MONOPOLY.BLOCK_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'RSMONOPOLY_BLOCK_LINK' => array(
		'NAME' => GetMessage('RS.MONOPOLY.BLOCK_LINK'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
    'RSMONOPOLY_SHOW_DESC_IN_SECTION' => array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_DESC_IN_SECTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
);

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('blockName','owlSupport'));
if( $arCurrentValues['RSMONOPOLY_USE_OWL']=='Y' ) {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('owlSettings'));
} else {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('bootstrapCols'));
}