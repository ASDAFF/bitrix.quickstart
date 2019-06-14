<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSMONOPOLY_AUTHOR_NAME' => array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_AUTHOR_JOB' => array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_JOB'),
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