<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('iblock')) {
    return;
}

$arResult['ADDITIONAL_BANNERS'] = array();
$superBannersIds = array();

foreach($arResult['ITEMS'] as $arItem) {
    $superBannersIds[] = $arItem['ID'];
}
$dbAdditionalBanners = CIBlockElement::GetList(
    array(
        'SORT' => 'ASC'
    ),
    array(
        'IBLOCK_ID' => $arParams['ADDITIONAL_BANNERS_IBLOCK'],
        'PROPERTY_SUPER_BANNER' => $superBannersIds,
		'IBLOCK_ACTIVE'=>'Y',
		'ACTIVE'=>'Y',
		'GLOBAL_ACTIVE'=>'Y',
    ),
    false,
    false,
    array(
        'ID',
        'NAME',
        'PREVIEW_PICTURE',
        'PROPERTY_IMAGE',
        'PROPERTY_HREF',
        'PROPERTY_SUPER_BANNER'
    )
);

while($arAdditionalBanner = $dbAdditionalBanners->Fetch()) {
    $id = (int) $arAdditionalBanner['PROPERTY_SUPER_BANNER_VALUE'];

    if(empty($arResult['ADDITIONAL_BANNERS'][$id]) && !is_array($arResult['ADDITIONAL_BANNERS'][$id])) {
        $arResult['ADDITIONAL_BANNERS'][$id] = array();
    }
    
    $arBanner = array(
        'NAME' => $arAdditionalBanner['NAME'],
        'LINK' => $arAdditionalBanner['PROPERTY_HREF_VALUE'],
    );
    
    if ($arAdditionalBanner['PREVIEW_PICTURE']) {
        $arBanner['IMAGE'] = CFile::GetPath($arAdditionalBanner['PREVIEW_PICTURE']);
    } elseif ($arAdditionalBanner['PROPERTY_IMAGE_VALUE']) {
        $arBanner['IMAGE'] = CFile::GetPath($arAdditionalBanner['PROPERTY_IMAGE_VALUE']);
    }

    $arResult['ADDITIONAL_BANNERS'][$id][] = $arBanner;
}
