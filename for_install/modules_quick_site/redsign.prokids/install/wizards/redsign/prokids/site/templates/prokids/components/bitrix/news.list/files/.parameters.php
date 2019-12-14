<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	// section, element
	'PROP_CODE_FILE' => array(
		'NAME' => GetMessage('PROP_CODE_FILE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
);