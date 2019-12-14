<?php

use \Bitrix\Main\Loader;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (
    !Loader::includeModule('iblock') ||
    !Loader::includeModule('catalog')
) {
	return;
}

$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while ($arr = $rsPrice->Fetch()) {
	$arPrice[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME_LANG'];
}

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if (intval($IBLOCK_ID) > 0) {
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
	while($arr = $rsProp->Fetch()) {
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	'POSITION_FIXED' => array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCCL_TPL_PARAM_TITLE_POSITION_FIXED'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	),
	'ADDITIONAL_PICT_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'OFFER_ADDITIONAL_PICT_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	// catalog
	'SKU_PRICE_SORT_ID' => array(
		'NAME' => getMessage('RS_SLINE.SKU_PRICE_SORT_ID'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arPrice),
		'DEFAULT' => '-',
	),
);

$arOffers = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if ($OFFERS_IBLOCK_ID) {
    $arProperty_Offers = array();
    $rsProp = CIBlockProperty::GetList(array('sort'=>'asc', 'name'=>'asc'), array('IBLOCK_ID' => $OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
    while ($arr = $rsProp->Fetch()) {
        $arr['ID'] = intval($arr['ID']);
        if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
            continue;
        $strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
        if ('' == $arr['CODE'])
            $arr['CODE'] = $arr['ID'];
        $arProperty_Offers[$arr['CODE']] = $strPropName;
    }

    $arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
        'DEFAULT' => '-',
    );
}
if (!isset($arCurrentValues['POSITION_FIXED']) || $arCurrentValues['POSITION_FIXED'] == 'Y')
{
	$positionList = array(
		'top left' => GetMessage('CP_BCCL_TPL_PARAM_POSITION_TOP_LEFT'),
		'top right' => GetMessage('CP_BCCL_TPL_PARAM_POSITION_TOP_RIGHT'),
		'bottom left' => GetMessage('CP_BCCL_TPL_PARAM_POSITION_BOTTOM_LEFT'),
		'bottom right' => GetMessage('CP_BCCL_TPL_PARAM_POSITION_BOTTOM_RIGHT')
	);
	$arTemplateParameters['POSITION'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BCCL_TPL_PARAM_TITLE_POSITION'),
		'TYPE' => 'LIST',
		'VALUES' => $positionList,
		'DEFAULT' => 'top left'
	);
	unset($positionList);
}
?>