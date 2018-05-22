<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);
$arCatalog = CCatalog::GetByID($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	// section, element
	'PROP_MORE_PHOTO' => array(
		'NAME' => GetMessage('PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'PROP_ARTICLE' => array(
		'NAME' => GetMessage('PROP_ARTICLE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'PROP_ACCESSORIES' => array(
		'NAME' => GetMessage('PROP_ACCESSORIES'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['E'],
	),
	'USE_FAVORITE' => array(
		'NAME' => GetMessage('USE_FAVORITE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_SHARE' => array(
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'SHOW_ERROR_EMPTY_ITEMS' => array(
		'NAME' => GetMessage('SHOW_ERROR_EMPTY_ITEMS'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'DONT_SHOW_LINKS' => array(
		'NAME' => GetMessage('DONT_SHOW_LINKS'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	'USE_AUTO_AJAXPAGES' => array(
		'NAME' => GetMessage('USE_AUTO_AJAXPAGES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	// store
	'USE_STORE' => array(
		'NAME' => GetMessage('USE_STORE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_MIN_AMOUNT' => array(
		'NAME' => GetMessage('USE_MIN_AMOUNT'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'MIN_AMOUNT' => array(
		'NAME' => GetMessage('MIN_AMOUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'MAIN_TITLE' => array(
		'NAME' => GetMessage('MAIN_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
);

if(IntVal($arCatalog["OFFERS_IBLOCK_ID"]))
{
	$listProp2 = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCatalog['OFFERS_IBLOCK_ID']);
	$arTemplateParameters['PROP_SKU_MORE_PHOTO'] = array(
		'NAME' => GetMessage('PROP_SKU_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['F'],
	);
	$arTemplateParameters['PROP_SKU_ARTICLE'] = array(
		'NAME' => GetMessage('PROP_SKU_ARTICLE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['SNL'],
	);
	$arTemplateParameters['PROPS_ATTRIBUTES'] = array(
		'NAME' => GetMessage('PROPS_ATTRIBUTES'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['SNL'],
		'MULTIPLE' => 'Y',
	);
}