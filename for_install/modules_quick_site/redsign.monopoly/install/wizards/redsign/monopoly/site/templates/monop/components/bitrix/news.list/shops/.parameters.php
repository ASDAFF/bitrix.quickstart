<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSMONOPOLY_PROP_CITY' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_CITY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	),
	'RSMONOPOLY_PROP_TYPE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	),
	'RSMONOPOLY_PROP_COORDINATES' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_COORDINATES'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['S'],
		'DEFAULT' => '',
	),
);

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('blockName'));