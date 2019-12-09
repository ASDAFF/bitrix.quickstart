<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSGOPRO_LINK' => array(
		'NAME' => GetMessage('RSGOPRO_LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_BANNER_TYPE' => array(
		'NAME' => GetMessage('RSGOPRO_BANNER_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_BLANK' => array(
		'NAME' => GetMessage('RSGOPRO_BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_TITLE1' => array(
		'NAME' => GetMessage('RSGOPRO_TITLE1'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_TITLE2' => array(
		'NAME' => GetMessage('RSGOPRO_TITLE2'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_PRICE' => array(
		'NAME' => GetMessage('RSGOPRO_PRICE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_TEXT' => array(
		'NAME' => GetMessage('RSGOPRO_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSGOPRO_CHANGE_SPEED' => array(
		'NAME' => GetMessage('RSGOPRO_CHANGE_SPEED'),
		'TYPE' => 'STRING',
		'DEFAULT' => '2000',
	),
	'RSGOPRO_CHANGE_DELAY' => array(
		'NAME' => GetMessage('RSGOPRO_CHANGE_DELAY'),
		'TYPE' => 'STRING',
		'DEFAULT' => '8000',
	),
	'RSGOPRO_BANNER_HEIGHT' => array(
		'NAME' => GetMessage('RSGOPRO_BANNER_HEIGHT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '402',
	),
	'RSGOPRO_BANNER_VIDEO_MP4' => array(
		'NAME' => GetMessage('RSGOPRO_BANNER_VIDEO_MP4'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'RSGOPRO_BANNER_VIDEO_WEBM' => array(
		'NAME' => GetMessage('RSGOPRO_BANNER_VIDEO_WEBM'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'RSGOPRO_BANNER_VIDEO_PIC' => array(
		'NAME' => GetMessage('RSGOPRO_BANNER_VIDEO_PIC'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
);