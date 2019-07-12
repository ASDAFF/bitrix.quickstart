<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.flyaway'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RS_LINK' => array(
		'NAME' => GetMessage('RS.LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_BLANK' => array(
		'NAME' => GetMessage('RS.BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_TEXT' => array(
		'NAME' => GetMessage('RS.TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_TEXT_PRICE' => array(
		'NAME' => GetMessage('RS.TEXT_PRICE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_TYPE_BANNER' => array(
		'NAME' => GetMessage('RS.TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_IS_HOVER_SCALE' => array(
		'NAME' => GetMessage('RS.FLYAWAY.IS_HOVER_SCALE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	),
);
