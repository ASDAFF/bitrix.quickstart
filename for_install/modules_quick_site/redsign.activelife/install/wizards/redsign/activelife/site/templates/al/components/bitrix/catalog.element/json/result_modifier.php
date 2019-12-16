<?php

use Bitrix\Main\Loader;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$bCatalog = Loader::includeModule('catalog');
$bDevFunc = Loader::includeModule('redsign.devfunc');

$arResult['OFFERS_IBLOCK_ID'] = 0;
$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
if (!empty($arSKU) && is_array($arSKU)) {
    $arResult['OFFERS_IBLOCK_ID'] = $arSKU['IBLOCK_ID'];
}

if ('' != $arParams['ADDITIONAL_PICT_PROP'] && '-' != $arParams['ADDITIONAL_PICT_PROP']) {
    $arParams['ADDITIONAL_PICT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP']);
} else {
    $arParams['ADDITIONAL_PICT_PROP'] = array();
}
if ('' != $arParams['ARTICLE_PROP'] && '-' != $arParams['ARTICLE_PROP']) {
    $arParams['ARTICLE_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ARTICLE_PROP']);
} else {
    $arParams['ARTICLE_PROP'] = array();
}

if ($arResult['OFFERS_IBLOCK_ID']) {
    if ('' != $arParams['OFFER_ADDITIONAL_PICT_PROP'] && '-' != $arParams['OFFER_ADDITIONAL_PICT_PROP']) {
        $arParams['ADDITIONAL_PICT_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
    }
    if ('' != $arParams['OFFER_ARTICLE_PROP'] && '-' != $arParams['OFFER_ARTICLE_PROP']) {
        $arParams['ARTICLE_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ARTICLE_PROP'];
    }
    if (is_array($arParams['OFFER_TREE_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_PROPS'];
        unset($arParams['OFFER_TREE_PROPS']);
        $arParams['OFFER_TREE_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
    }
}

if ($bDevFunc) {
    $params = array(
        'RESIZE' => array(
            'smaller' => array(
                'MAX_WIDTH' => 90,
                'MAX_HEIGHT' => 90,
            ),
            'small' => array(
                'MAX_WIDTH' => 210,
                'MAX_HEIGHT' => 160,
            ),
            'big' => array(
                'MAX_WIDTH' => 550,
                'MAX_HEIGHT' => 500,
            ),
        ),
        'PREVIEW_PICTURE' => true,
        'DETAIL_PICTURE' => true,
        'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP']
    );
    RSDevFunc::getElementPictures($arResult, $params);
}

$arPriceTypeID = array();
foreach ($arResult['CAT_PRICES'] as $value) {
    $arPriceTypeID[] = $value['ID'];
}

$arElementsIDs = array(
    $arResult['ID'] => 0
);
$arElementJs = array();
$arOffersJs = array();
$arSortProps = array();

if (is_array($arResult['OFFERS']) && 0 < count($arResult['OFFERS'])) {

    foreach ($arResult['OFFERS'] as $iOfferKey => $arOffer) {

        // USE_PRICE_COUNT fix
        if (!isset($arElementsIDs[$arOffer['ID']])) {
            $arElementsIDs[$arOffer['ID']] = $iOfferKey;
        } else {
            if (isset($arResult['OFFERS_SELECTED']) && $arResult['OFFERS_SELECTED'] == $iOfferKey) {
                $arResult['OFFERS_SELECTED'] = $arElementsIDs[$arOffer['ID']];
            }
            unset($arResult['OFFERS'][$iOfferKey]);
            continue;
        }

        $arOfferJs = array(
            'ID' => $arOffer['ID'],
            'NAME' => $arOffer['NAME'],
            'DETAIL_PAGE_URL' => $arOffer['DETAIL_PAGE_URL'],
            'PROPERTIES' => '',
            'PRICES' => '',
            'CAN_BUY' => $arOffer['CAN_BUY'],
            'ADD_URL' => $arOffer['ADD_URL'],
            'CATALOG_MEASURE_RATIO' => $arOffer['CATALOG_MEASURE_RATIO'],
            'CATALOG_MEASURE_NAME' => $arOffer['CATALOG_MEASURE_NAME'],
            'CATALOG_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
        );

        // images
        if ($arOffer['PRODUCT_PHOTO']) {
            foreach ($arOffer['PRODUCT_PHOTO'] as $sImageKey => $arImage) {
                $arPhotos = array('original' => $arImage['SRC']);
                if ($arImage['RESIZE']) {
                    foreach ($arImage['RESIZE'] as $sSize => $arPhoto) {
                        $arPhotos[$sSize] = $arPhoto['src'];
                    }
                }
                $arOfferJs['IMAGES'][$sImageKey] = $arPhotos;
            }
        }

        // properties
        
        foreach ($arOffer['DISPLAY_PROPERTIES'] as $propCode => $arProp) {

			if (in_array($arProp['CODE'], $arParams['OFFER_TREE_PROPS'][$arResult['OFFERS_IBLOCK_ID']])) {

                if (isset($arProp)) {
                    $arOfferJs['PROPERTIES'][$arProp['CODE']] = $arProp['DISPLAY_VALUE'];
                    if (!in_array($arProp['CODE'], $arSortProps)) {
                        $arSortProps[] = $arProp['CODE'];
                    }
                } else if (isset($arOffer['PROPERTIES'][$arProp['CODE']]) && $arOffer['PROPERTIES'][$arProp['CODE']]['VALUE'] != '') {
                    $arOfferJs['PROPERTIES'][$arProp['CODE']] = $arOffer['PROPERTIES'][$arProp['CODE']]['VALUE'];
                    if (!in_array($arProp['CODE'], $arSortProps)) {
                        $arSortProps[] = $arProp['CODE'];
                    }
                }

            }

        }

        foreach ($arOffer['DISPLAY_PROPERTIES'] as $arProp) {
            $arOfferJs['DISPLAY_PROPERTIES'][$arProp['ID']] = array(
                'NAME' => $arProp['NAME'],
                'DISPLAY_VALUE' => is_array($arProp['DISPLAY_VALUE']) ? implode(' / ', $arProp['DISPLAY_VALUE']) : $arProp['DISPLAY_VALUE']

            );
        }

        // prices
        if ($arParams['USE_PRICE_COUNT']) {

            if ($bDevFunc) {
                RSDevFunc::getPriceMatrixEx($arOffer, 0, $arPriceTypeID, 'Y', $arResult['CONVERT_CURRENCY']);
            }
            if (isset($arOffer['PRICE_MATRIX'])) {
                $arOfferJs['PRICE_MATRIX'] = $arOffer['PRICE_MATRIX'];
            }

        } else {
            foreach ($arParams['PRICE_CODE'] as $priceCode) {
                if (isset($arOffer['PRICES'][$priceCode])) {
                    $arOfferJs['PRICES'][$arOffer['PRICES'][$priceCode]['PRICE_ID']] = array(
                        'PRICE_ID' => $arOffer['PRICES'][$priceCode]['PRICE_ID'],
                        'VALUE' => $arOffer['PRICES'][$priceCode]['VALUE'],
                        'PRINT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_VALUE'],
                        'DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['DISCOUNT_VALUE'],
                        'PRINT_DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_VALUE'],
                        'DISCOUNT_DIFF' => $arOffer['PRICES'][$priceCode]['DISCOUNT_DIFF'],
                        'PRINT_DISCOUNT' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_DIFF'],
                    );
                }

                if (isset($arOffer['MIN_PRICE'])) {
                    $arOfferJs['MIN_PRICE'] = $arOffer['MIN_PRICE'];
                }
            }
        }

        $arOffersJs[$arOffer['ID']] = $arOfferJs;
    }

    $iTime = ConvertTimeStamp(time(),'FULL');
    // add quickbuy
    if (Bitrix\Main\Loader::includeModule('redsign.quickbuy')) {
        $arFilter = array(
            'DATE_FROM' => $iTime,
            'DATE_TO' => $iTime,
            'QUANTITY' => 0,
            'ELEMENT_ID' => $arElementsIDs,
        );
        $dbRes = CRSQUICKBUYElements::GetList(array('ID'=>'SORT'), $arFilter);
        while ($arData = $dbRes->Fetch()) {
            if ($arData['ELEMENT_ID'] == $arResult['ID']) {
                $arElementJs['QUICKBUY'] = $arData;
                $arElementJs['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
            } elseif (isset($arOffersJs[$arData['ELEMENT_ID']])) {
                $arOffersJs[$arData['ELEMENT_ID']]['QUICKBUY'] = $arData;
                $arOffersJs[$arData['ELEMENT_ID']]['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
            }
        }
    }

    // add da2
    if (Bitrix\Main\Loader::includeModule('redsign.daysarticle2')) {
        $arFilter = array(
            'DATE_FROM' => $iTime,
            'DATE_TO' => $iTime,
            'QUANTITY' => 0,
            'ELEMENT_ID' => $arElementsIDs,
        );
        $dbRes = CRSDA2Elements::GetList(array('ID'=>'SORT'), $arFilter);
        while ($arData = $dbRes->Fetch()) {
            if ($arData['ELEMENT_ID'] == $arResult['ID']) {
                $arElementJs['DAYSARTICLE2'] = $arData;
                $arElementJs['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
            } elseif (isset($arOffersJs[$arData['ELEMENT_ID']])) {
                $arOffersJs[$arData['ELEMENT_ID']]['DAYSARTICLE2'] = $arData;
                $arOffersJs[$arData['ELEMENT_ID']]['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
            }
        }
    }
    
    $arElementJs['CATALOG_SUBSCRIBE'] = ($arResult['CATALOG_SUBSCRIBE'] == 'Y');
}

$arResult['JSON_EXT'] = array(
    'PARAMS' => array(
        'USE_STORE' => ('N' != $arParams['USE_STORE'] ? true : false),
        'USE_MIN_AMOUNT' => ('N' != $arParams['USE_MIN_AMOUNT'] ? true : false),
        'MIN_AMOUNT' => intval($arParams['MIN_AMOUNT'])
    ),
    'ELEMENT' => $arElementJs,
    'SORT_PROPS' => $arSortProps,
    'OFFERS' => $arOffersJs,
);