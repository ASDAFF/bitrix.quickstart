<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!CModule::IncludeModule('redsign.devfunc')
    || !CModule::IncludeModule('redsign.flyaway')
    || !CModule::IncludeModule('iblock')) {
    return;
}

if (!empty($arResult)) {
    if (is_array($arResult['OFFERS']) && count($arResult['OFFERS']) > 0) {
        // Get sorted properties
        $arResult['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties($arResult['OFFERS'], $arParams['OFFER_TREE_PROPS']);
        // /Get sorted properties
    }

  // get other data
    $params = array(
        'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
        'PROP_SKU_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO'],
        'MAX_WIDTH' => 120,
        'MAX_HEIGHT' => 120,
        'PAGE' => 'detail',
    );

    $arItems = array(0 => &$arResult);
    RSDevFunc::GetDataForProductItem($arItems, $params);
  // /get other data

    // get no photo
    $arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH' => $max_width_size, 'MAX_HEIGHT' => $max_height_size));
    // /get no photo

    // quantity for bitrix:catalog.store.amount
    $arQuantity[$arResult['ID']] = $arResult['CATALOG_QUANTITY'];
    foreach ($arResult['OFFERS'] as $key => $arOffer) {
        $arQuantity[$arOffer['ID']] = $arOffer['CATALOG_QUANTITY'];
    }

    $arResult['DATA_QUANTITY'] = $arQuantity;

    // get SKU_IBLOCK_ID
    $arResult['OFFERS_IBLOCK'] = 0;
    $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);

    if (!empty($arSKU) && is_array($arSKU)) {
        $arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
    }

    // QB and DA2
    $arResult['HAVE_DA2'] = 'N';
    $arResult['HAVE_QB'] = 'N';

    if (is_array($arResult['OFFERS']) && count($arResult['OFFERS']) > 0) {
        foreach ($arResult['OFFERS'] as $arOffer) {
            if (isset($arOffer['DAYSARTICLE2'])) {
                $arResult['HAVE_DA2'] = 'Y';
            }

            if (isset($arOffer['QUICKBUY'])) {
                $arResult['HAVE_QB'] = 'Y';
            }
        }
    }

    if (isset($arResult['DAYSARTICLE2'])) {
        $arResult['HAVE_DA2'] = 'Y';
    }

    if (isset($arResult['QUICKBUY'])) {
        $arResult['HAVE_QB'] = 'Y';
    }
    // /QB and DA2
}

// tabs
$arResult['TABS'] = array(
    'DETAIL_TEXT' => false,                // description
    'DISPLAY_PROPERTIES' => false,        // grouped props
    'SET' => false,                        // set
    'PROPS_TABS' => false,                // tabs from properties
);

if ($arResult['HAVE_SET']) {
    $arResult['TABS']['SET'] = true;
}

if (is_array($arResult['OFFERS']) && count($arResult['OFFERS']) > 0) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        if ($arOffer['HAVE_SET']) {
            $arResult['TABS']['SET'] = true;
            break;
        }
    }
}

if ($arResult['DETAIL_TEXT'] != '') {
    $arResult['TABS']['DETAIL_TEXT'] = true;
}

if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0) {
    $arResult['TABS']['DISPLAY_PROPERTIES'] = true;
}

if (is_array($arParams['PROPS_TABS']) && count($arParams['PROPS_TABS']) > 0) {
    foreach ($arParams['PROPS_TABS'] as $sPropCode) {
        if ($sPropCode != '' &&
            (
                (isset($arResult['PROPERTIES'][$sPropCode]['VALUE'])) ||
                ($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'F' && isset($arResult['PROPERTIES'][$sPropCode]['VALUE'])) ||
                ($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'E' && isset($arResult['PROPERTIES'][$sPropCode]['VALUE']))
            )
        ) {
            $arResult['TABS']['PROPS_TABS'] = true;
            if ($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'F') {
                if (is_array($arResult['PROPERTIES'][$sPropCode]['VALUE'])) {
                    foreach ($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $keyF => $fileID) {
                        $rsFile = CFile::GetByID($fileID);
                        if ($arFile = $rsFile->Fetch()) {
                            $arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF] = $arFile;
                            $tmp = explode('.', $arFile['FILE_NAME']);
                            $tmp = end($tmp);
                            $type = 'other';
                            switch ($tmp) {
                                case 'docx':
                                                                case 'doc':
                                    $type = 'word';
                                    break;
                                case 'pdf':
                                    $type = 'pdf';
                                    break;
                                case 'xls':
                                    $type = 'excel';
                                    break;
                                case 'xlsx':
                                    $type = 'excel';
                                    break;
                            }
                            $arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['SRC'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
                            $arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['TYPE'] = $type;
                            $arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'], 1);
                            $arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['EXTENSION'] = $type;
                        }
                    }
                } else {
                    $fileID = $arResult['PROPERTIES'][$sPropCode]['VALUE'];
                    $rsFile = CFile::GetByID($fileID['ID']);
                    if ($arFile = $rsFile->Fetch()) {
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'] = array();
                        $tmp = explode('.', $arFile['FILE_NAME']);
                        $tmp = end($tmp);
                        $type = 'other';
                        $type2 = '';
                        switch ($tmp) {
                            case 'doc':
                            case 'docx':
                                $type = 'doc';
                                break;
                            case 'xls':
                            case 'xlsx':
                                $type = 'excel';
                                break;
                            case 'pdf':
                                $type = 'pdf';
                                break;
                        }
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['SRC'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['TYPE'] = $type;
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'], 1);
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['EXTENSION'] = $tmp;
                    }
                }
            }
        }
    }
}

$arResult['ACCESSORIES'] = array();
$arResult['DOCUMENT'] = array();

$nav = CIBlockSection::GetNavChain(false, $arResult['IBLOCK_SECTION_ID']);
$nav = $nav->GetNext();
$arResult['ROOT_SECTION_ID'] = $nav['ID'];

if (
    count($arResult['PRICES']) <= 1 && isset($arResult['MIN_PRICE']) &&
    strlen($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE']]['VALUE']) > 0 &&
    strlen($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']]['VALUE']) > 0
) {
    $arResult['RS_ADD_MEASURE'] = array();
    $arResult['RS_ADD_MEASURE']['CURRENCY'] = $arResult['MIN_PRICE']['CURRENCY'];
    $arResult['RS_ADD_MEASURE']['MEASURE_NAME'] = $arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE']]['VALUE'];
    $arResult['RS_ADD_MEASURE']['MEASURE_RATIO'] = $arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']]['VALUE'];
    $arResult['RS_ADD_MEASURE']['MEASURE_FACTOR'] = $arResult['CATALOG_MEASURE_RATIO'] / $arResult['RS_ADD_MEASURE']['MEASURE_RATIO'];

    $arResult['RS_ADD_MEASURE']['PRICE'] = array();
    $arResult['RS_ADD_MEASURE']['PRICE']['VALUE'] = $arResult['MIN_PRICE']['VALUE'] * $arResult['RS_ADD_MEASURE']['MEASURE_FACTOR'];
    $arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_VALUE'] = FormatCurrency($arResult['RS_ADD_MEASURE']['PRICE']['VALUE'], $arResult['RS_ADD_MEASURE']['CURRENCY']);
    $arResult['RS_ADD_MEASURE']['PRICE']['DISCOUNT_VALUE'] = $arResult['MIN_PRICE']['DISCOUNT_VALUE'] * $arResult['RS_ADD_MEASURE']['MEASURE_RATIO'];
    $arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_DISCOUNT_VALUE'] = FormatCurrency($arResult['RS_ADD_MEASURE']['PRICE']['DISCOUNT_VALUE'], $arResult['RS_ADD_MEASURE']['CURRENCY']);
    $arResult['RS_ADD_MEASURE']['PRICE']['TOTAL_VALUE'] = $arResult['RS_ADD_MEASURE']['PRICE']['VALUE'] * $arResult['CATALOG_MEASURE_RATIO'];
    $arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_TOTAL_VALUE'] = FormatCurrency($arResult['RS_ADD_MEASURE']['PRICE']['TOTAL_VALUE'], $arResult['RS_ADD_MEASURE']['CURRENCY']);
    $arResult['RS_ADD_MEASURE']['PRICE']['TOTAL_DISCOUNT_VALUE'] =   $arResult['MIN_PRICE']['VALUE'] * $arResult['CATALOG_MEASURE_RATIO'];
    $arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_TOTAL_DISCOUNT_VALUE'] = FormatCurrency($arResult['RS_ADD_MEASURE']['PRICE']['TOTAL_DISCOUNT_VALUE'],  $arResult['RS_ADD_MEASURE']['CURRENCY']);
}

/* JSON OFFERS */
$arResult['JSON_EXT'] = RSDevFuncOffersExtension::GetJSONElement(
    $arResult,
    $arParams['OFFER_TREE_PROPS'],
    $arParams['PRICE_CODE'],
    array(
        'SKU_MORE_PHOTO_CODE' => $arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO'],
        'SIZES' => array(
            'WIDTH' => 300,
            'HEIGHT' => 300,
        ),
        'SKU_ARTICLE_CODE' => $arParams['RSFLYAWAY_PROP_ARTICLE'],
    )
);

$arResult['JSON_EXT']['CAT_PRICES'] = $arResult['CAT_PRICES'];

if (!empty($arResult['OFFERS']) && is_array($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        if (empty($arResult['JSON_EXT']['OFFERS'][$arOffer['ID']])) {
            continue;
        }

        $arResult['JSON_EXT']['OFFERS'][$arOffer['ID']]['QUANTITY'] = $arOffer['CATALOG_QUANTITY'];
    }
}
/* /JSON OFFERS */
