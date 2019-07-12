<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog')){

$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while($arr = $rsPrice->Fetch())
	$arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];

$arTemplateParameters = array(
	'CUSTOM_PRICE_CODE' => array(
		'NAME' => GetMessage('CUSTOM_PRICE_CODE'),
		'TYPE' => 'LIST',
		'VALUES' => $arPrice,
	),
	'MAX_WIDTH' => array(
		'NAME' => GetMessage('MAX_WIDTH'),
		'TYPE' => 'STRING',
	),
	'MAX_HEIGHT' => array(
		'NAME' => GetMessage('MAX_HEIGHT'),
		'TYPE' => 'STRING',
	),
	'DISPLAY_TITLE_TEXT' => array(
		'NAME' => GetMessage('DISPLAY_TITLE_TEXT'),
		'TYPE' => 'STRING',
	),
	'PAGE_URL_LIST' => array(
		'NAME' => GetMessage('PAGE_URL_LIST'),
		'TYPE' => 'STRING',
	),
	'PROPCODE_IMAGES' => array(
		'NAME' => GetMessage('PROPCODE_IMAGES'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'SKU_MORE_PHOTO'
	),
);
}