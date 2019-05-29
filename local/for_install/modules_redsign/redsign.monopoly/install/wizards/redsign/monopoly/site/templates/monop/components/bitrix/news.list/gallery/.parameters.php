<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array();

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('blockName','owlSupport'));
if( $arCurrentValues['RSMONOPOLY_USE_OWL']=='Y' ) {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('owlSettings'));
} else {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('bootstrapCols'));
}
if( $arCurrentValues['RSMONOPOLY_SHOW_BLOCK_NAME']=='Y' ) {
    $arTemplateParameters['RSMONOPOLY_BLOCK_NAME'] = array(
        'NAME' => GetMessage('RSMONOPOLY_BLOCK_NAME'),
        'TYPE' => 'TEXT'
    );
}
