<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSMONOPOLY_PROP_VACANCY_TYPE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_VACANCY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	),
	'RSMONOPOLY_PROP_SIGNATURE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_SIGNATURE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'DEFAULT' => '',
	),
);

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('blockName'));