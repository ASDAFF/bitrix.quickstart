<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;

$arIblockCatalogIDs = array();

foreach ($arParams as $name => $prop) {
    if (preg_match('/^ADDITIONAL_PICT_PROP_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['ADDITIONAL_PICT_PROP'][$iBlockID] = $arParams[$name];
            $arIblockCatalogIDs[] = $iBlockID;
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^ARTICLE_PROP_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['ARTICLE_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    /*
    if (preg_match('/^OFFER_TREE_PROPS_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['OFFER_TREE_PROPS'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^OFFER_TREE_COLOR_PROPS_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['OFFER_TREE_COLOR_PROPS'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^OFFER_TREE_BTN_PROPS_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['OFFER_TREE_BTN_PROPS'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    */
}

if (Loader::includeModule('redsign.activelife')) {

    $c = Option::get('redsign.activelife', 'color_table_count', 0);
    $arResult['COLORS_TABLE']  = array();
    for ($i = 0; $i < $c; $i++) {
        $name = Option::get('redsign.activelife', 'color_table_name_'.$i, '');
        $rgb = Option::get('redsign.activelife', 'color_table_rgb_'.$i, '');
        if ($name != '' && $rgb != '') {
            $arResult['COLORS_TABLE'][ToUpper($name)] = array(
                'NAME' => $name,
                'RGB' => $rgb,
            );
        }
    }
}

if (Loader::includeModule('iblock')) {
    if (is_array($arResult['GRID']['ROWS']) && count($arResult['GRID']['ROWS'])) {
        $arElements = array();
        $params = array(
            'RESIZE' => array(
                'small' => array(
                    'MAX_WIDTH' => 210,
                    'MAX_HEIGHT' => 160,
                ),
            ),
            'PREVIEW_PICTURE' => true,
            'DETAIL_PICTURE' => true,
            'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
        );

        foreach ($arResult['GRID']['ROWS'] as $iItemKey => $arItem) {
            $arElements[$arItem['PRODUCT_ID']] = &$arResult['GRID']['ROWS'][$iItemKey];
            $arIBlockIDs[] = $arItem['IBLOCK_ID'];
        }

        $bPictureIsset = false;
        
        $arFilter = array(
            'ID' => array_keys($arElements)
        );
        $arSelect = array(
            'IBLOCK_ID',
            'ID',
            'NAME',
            'PREVIEW_PICTURE',
            'DETAIL_PICTURE',
        );

        if (is_array($arParams['ADDITIONAL_PICT_PROP']) && count($arParams['ADDITIONAL_PICT_PROP']) > 0) {
            foreach ($arParams['ADDITIONAL_PICT_PROP'] as $iIblockID => $PropCode) {
                $arFilter['IBLOCK_ID'][] = $iIblockID;
                $arSelect[] = 'PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$iIblockID];

            }
        }
        $dbElements = CIBlockElement::getList(array(), $arFilter, false, false, $arSelect    );


        while ($arElement = $dbElements->getNext()) {

             $arElements[$arElement['ID']]['IBLOCK_ID'] = $arElement['IBLOCK_ID'];

            if (intval($arElement['PREVIEW_PICTURE']) > 0) {
                $arElements[$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['PREVIEW_PICTURE'];
                continue;
            }
            if (intval($arElement['DETAIL_PICTURE']) > 0) {
                $arElements[$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['DETAIL_PICTURE'];
                continue;
            }
            
            if (intval($arElement['PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']].'_VALUE']) > 0) {
                $arElements[$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']].'_VALUE'];
                continue;
            }
        }

        foreach ($arResult['GRID']['ROWS'] as $iItemKey => $arItem) {
            $arResult['GRID']['ROWS'][$iItemKey]['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult['GRID']['ROWS'][$iItemKey], $params, 1);
        }
    }    
}

$arResult['BUY1CLICK_STRING'] = '';

if ($arResult['ShowReady'] == 'Y') {
    foreach ($arResult["GRID"]["ROWS"] as $k => $arItem) {
        if($arItem['DELAY'] == 'Y' || $arItem['CAN_BUY'] == 'N') {
            continue;
        }

        $arResult['BUY1CLICK_STRING'] .= '['.$arItem['ID'].'] '.$arItem['NAME'].', ';
    }
}