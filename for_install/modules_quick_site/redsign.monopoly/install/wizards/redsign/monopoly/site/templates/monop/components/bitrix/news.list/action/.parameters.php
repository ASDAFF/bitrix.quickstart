<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSMONOPOLY_PROP_MARKER_TEXT' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_PROP_MARKER_COLOR' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_COLOR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_PROP_ACTION_DATE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_ACTION_DATE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
);

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('blockName','owlSupport'));
if( $arCurrentValues['RSMONOPOLY_USE_OWL']=='Y' ) {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('owlSettings'));
} else {
	RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('bootstrapCols'));
}