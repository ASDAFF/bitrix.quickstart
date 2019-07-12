<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('iblock'))
	return;

if(!CModule::IncludeModule('redsign.flyaway'))
	return;

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSFLYAWAY_PROP_CITY' => array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_CITY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	),
	'RSFLYAWAY_PROP_TYPE' => array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	),
	'RSFLYAWAY_PROP_COORDINATES' => array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_COORDINATES'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['S'],
		'DEFAULT' => '',
	),
);

RSFLYAWAY_AddComponentParameters($arTemplateParameters,array('blockName'));