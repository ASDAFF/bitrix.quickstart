<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!\Bitrix\Main\Loader::includeModule('redsign.devfunc')
	|| !\Bitrix\Main\Loader::includeModule('iblock')
	|| !\Bitrix\Main\Loader::includeModule('catalog')
){
	return;
}

$res = CIBlock::GetList(array('SORT'=>'ASC'), array('ACTIVE'=>'Y'));
while($arIblock = $res->Fetch()) {
	$arIBlocks[$arIblock['ID']] = $arIblock['NAME'];
}
$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while($arr = $rsPrice->Fetch()) {
	$arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];
}

$arTemplates = array(
	'showcase' => getMessage('FLW_SHOWCASE_SEARCH'),
	'table' => getMessage('FLW_TABLE_SEARCH'),
	'gallery' => getMessage('FLW_GALLERY_SEARCH'),
);

$arTemplateParameters = array(
	'TEMPLATE_VIEW' => array(
		'NAME' => getMessage('FLW.TEMPLATE_VIEW_SEARCH'),
		'TYPE' => 'LIST',
		'VALUES' => $arTemplates,
		'DEFAULT' => 'showcase',
		'REFRESH' => 'Y',
	),
	'IBLOCK_ID' => array(
		'NAME' => GetMessage('FLW.PARAM_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlocks,
		'REFRESH' => 'Y',
	),
	'PRICE_CODE' => array(
		'NAME' => GetMessage('FLW.PARAM_PRICE_CODE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arPrice,
	),
);
if ('showcase' == $arCurrentValues['TEMPLATE_VIEW'])
{
	$arTemplateParameters['USE_HOVER_POPUP'] = array(
		'NAME' => getMessage('FLW.USE_HOVER_POPUP'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
}

$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues["IBLOCK_ID"]);
if (!empty($arSKU) && is_array($arSKU))
{
	$listProp2 = RSDevFuncParameters::GetTemplateParamsPropertiesList($arSKU["IBLOCK_ID"]);
	$arTemplateParameters['RSFLYAWAY_PROP_SKU_MORE_PHOTO'] = array(
		'NAME' => GetMessage('FLW.PROP_SKU_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['F'],
	);
	$arTemplateParameters['OFFER_TREE_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('FLW.OFFER_TREE_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['SNL'],
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	
	$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('FLW.OFFER_TREE_COLOR_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' =>  $listProp2['HL'],
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);	
}

?>
