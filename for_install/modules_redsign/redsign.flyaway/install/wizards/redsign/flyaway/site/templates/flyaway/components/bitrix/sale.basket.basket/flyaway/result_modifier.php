<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('catalog') ||
   !Loader::includeModule('sale')) {
    return;
}

$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arResult['URLS'] = array(
    'delete' => $curPage."delete&id=#ID#",
    'delay' => $curPage."delay&id=#ID#",
    'add' => $curPage."add&id=#ID#"
);

$arResult['BUTTONS'] = array();
$arResult['BUTTONS']['DELETE'] = in_array('DELETE', $arParams['COLUMNS_LIST']);
$arResult['BUTTONS']['DELAY'] = in_array('DELAY', $arParams['COLUMNS_LIST']);

if(!empty($arResult['GRID']['ROWS'])) {

    $arItemsIds = array();
        foreach($arResult['GRID']['ROWS'] as $arItem) {
            $arItemIds[] = $arItem['PRODUCT_ID'];
        }

        /* Исправить когда нибудь */
        $arResult['DESCRIPTIONS'] = array();
        $resElements = CIBlockElement::GetList(
        array(),
        array(
            'ID' => $arItemIds,
            'IBLOCK_TYPE' => 'catalog'
        ),
        false,
        false,
        array('ID', 'PREVIEW_TEXT', 'DETAIL_TEXT')
    );

    while($arItem = $resElements->GetNext()) {
        if(!empty($arItem['PREVIEW_TEXT'])) {
            $arResult['DESCRIPTIONS'][$arItem['ID']] = $arItem['PREVIEW_TEXT'];
        } elseif(!empty($arItem['DETAIL_TEXT'])) {
            $arResult['DESCRIPTIONS'][$arItem['ID']] = $arItem['DETAIL_TEXT'];
        } else {
            $arResult['DESCRIPTIONS'][$arItem['ID']] = '';
        }
    }
    /* /Исправить когда нибудь */

    /* Buy1Click tring */
    $arResult['BUY1CLICK_STRING'] = '';
    foreach ($arResult["GRID"]["ROWS"] as $k => $arItem) {
        if($arItem['DELAY'] == 'Y' || $arItem['CAN_BUY'] == 'N') {
            continue;
        }

        $arResult['BUY1CLICK_STRING'] .= '['.$arItem['ID'].'] '.$arItem['NAME'].', ';
    }
}
