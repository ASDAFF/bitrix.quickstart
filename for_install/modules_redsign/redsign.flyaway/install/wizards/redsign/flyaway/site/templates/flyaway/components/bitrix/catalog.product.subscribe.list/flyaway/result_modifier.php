<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;

if (!Loader::IncludeModule('redsign.devfunc')
    || !Loader::IncludeModule('redsign.flyaway')
    || !Loader::IncludeModule('redsign.favorite')) {
    return;
}

$OFFER_IBLOCK_ID = 0;
if ($arResult['CATALOG']['PRODUCT_IBLOCK_ID'] == '0') {
    $IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
} else {
    $IBLOCK_ID = $arResult['CATALOG']['PRODUCT_IBLOCK_ID'];
    $OFFER_IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
}

$arResult['TEMPLATE_DEFAULT'] = array(
    'TEMPLATE' => 'showcase',
    'CSS' => 'products_showcase',
);

if ($arParams['RSFLYAWAY_TEMPLATE'] == 'showcase_mob') {
    $arResult['TEMPLATE_DEFAULT'] = array(
        'TEMPLATE' => 'showcase',
        'CSS' => 'products_showcase products_showcase-mob',
    );
} elseif ($arParams['RSFLYAWAY_TEMPLATE'] == 'list') {
    $arResult['TEMPLATE_DEFAULT'] = array(
        'TEMPLATE' => 'list',
        'CSS' => 'products_list',
    );
} elseif ($arParams['RSFLYAWAY_TEMPLATE'] == 'list_little') {
    $arResult['TEMPLATE_DEFAULT'] = array(
        'TEMPLATE' => 'list_little',
        'CSS' => 'products_table',
    );
}

if ($OFFER_IBLOCK_ID) {
    if ('' != $arParams['OFFER_ADDITIONAL_PICT_PROP'] && '-' != $arParams['OFFER_ADDITIONAL_PICT_PROP']) {
        if (is_array($arParams['ADDITIONAL_PICT_PROP'])) {
            $arParams['ADDITIONAL_PICT_PROP'][$OFFER_IBLOCK_ID] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
        } else {
            $arParams['ADDITIONAL_PICT_PROP'] = array($OFFER_IBLOCK_ID => $arParams['OFFER_ADDITIONAL_PICT_PROP']);
        }
    }

    if (is_array($arParams['OFFER_TREE_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_PROPS'];
        unset($arParams['OFFER_TREE_PROPS']);
        $arParams['OFFER_TREE_PROPS'] = array($OFFER_IBLOCK_ID => $arProps);
    }
    if (is_array($arParams['OFFER_TREE_COLOR_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_COLOR_PROPS'];
        unset($arParams['OFFER_TREE_COLOR_PROPS']);
        $arParams['OFFER_TREE_COLOR_PROPS'] = array($OFFER_IBLOCK_ID => $arProps);
    }
}

$max_width_size = 300;
$max_height_size = 300;
$params = array(
    'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
    'MAX_WIDTH' => $max_width_size,
    'MAX_HEIGHT' => $max_height_size,
);

// get other data
RSDevFunc::GetDataForProductItem($arResult['ITEMS'], $params);
// /get other data

// QB and DA2
$arSectionFilter = array();

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
    foreach ($arResult['ITEMS'] as $key1 => $arItem) {
        if (!in_array($arItem['~IBLOCK_SECTION_ID'], $arSectionFilter)) {
            $arSectionFilter[] = $arItem['~IBLOCK_SECTION_ID'];
        }

        // quantity for bitrix:catalog.store.amount
        $arQuantity[$arItem['ID']] = $arItem['CATALOG_QUANTITY'];
        if (is_array($arItem['OFFERS']) && count($arItem['OFFERS']) > 0) {
            foreach ($arItem['OFFERS'] as $key => $arOffer) {
                $arQuantity[$arOffer['ID']] = $arOffer['CATALOG_QUANTITY'];
            }
        }
        $arResult['ITEMS'][$key1]['DATA_QUANTITY'] = $arQuantity;

        // QB and DA2
        $arResult['ITEMS'][$key1]['HAVE_DA2'] = 'N';
        $arResult['ITEMS'][$key1]['HAVE_QB'] = 'N';
        $arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] = (intval($arItem['CATALOG_QUANTITY']) > 0 ? $arItem['CATALOG_QUANTITY'] : 0);
        $MIN_PRICE = 9999999999999;
        if (is_array($arItem['OFFERS']) && count($arItem['OFFERS']) > 0) {
            foreach ($arItem['OFFERS'] as $arOffer) {
                if (isset($arOffer['DAYSARTICLE2'])) {
                    $arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
                }
                if (isset($arOffer['QUICKBUY'])) {
                    $arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
                }
                $arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] = $arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] + $arOffer['CATALOG_QUANTITY'];
                if ($arOffer['MIN_PRICE']['DISCOUNT_VALUE'] < $MIN_PRICE) {
                    $MIN_PRICE = $arOffer['MIN_PRICE']['DISCOUNT_VALUE'];
                    $arResult['ITEMS'][$key1]['OUT_PRICE'] = $arOffer['MIN_PRICE'];
                }
            }
        } else {
            $arResult['ITEMS'][$key1]['OUT_PRICE'] = $arItem['MIN_PRICE'];
        }
        if (isset($arItem['DAYSARTICLE2'])) {
            $arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
        }
        if (isset($arItem['QUICKBUY'])) {
            $arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
        }
        // /QB and DA2
    }
}
// /QB and DA2

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH' => $max_width_size, 'MAX_HEIGHT' => $max_height_size));
// /get no photo

// get flyaway data
$params = array(
    'PREVIEW_PICTURE' => true,
    'DETAIL_PICTURE' => true,
    'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
    'RESIZE' => array(
        0 => array(
            'MAX_WIDTH' => $max_width_size,
            'MAX_HEIGHT' => $max_height_size,
        ),
    ),
);

RsFlyaway::addData($arResult['ITEMS'], $params);
// /get flyaway data

if ('Y' == $arParams['SHOW_SECTION_URL']) {
    if (!empty($arResult['ITEMS'])) {
        $arResult['SECTIONS'] = array();
        foreach ($arResult['ITEMS'] as $arItem) {
            $arResult['SECTIONS'][$arItem['~IBLOCK_SECTION_ID']] = $arItem['~IBLOCK_SECTION_ID'];
        }

        if (!empty($arResult['SECTIONS'])) {
            $dbSections = CIBlockSection::GetList(array(), array('ID' => $arResult['SECTIONS']));
            while ($arSection = $dbSections->GetNext()) {
                $arResult['SECTIONS'][$arSection['ID']] = $arSection;
            }
        }
    }
}

if (is_array($arResult['ITEMS'])) {
    RSDevFunc::GetDataForProductItem($arResult['ITEMS']);
    foreach ($arResult['ITEMS'] as $iItemKey => $arItem) {
        if (!empty($arItem['OFFERS'])) {
            $arResult['ITEMS'][$iItemKey]['OFFERS_SELECTED'] = $iOfferSel = isset($arItem['OFFERS_SELECTED']) ? $arItem['OFFERS_SELECTED'] : 0;

            if (!empty($arParams['OFFER_TREE_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']])) {
                $arResult['ITEMS'][$iItemKey]['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties($arItem['OFFERS'], $arParams['OFFER_TREE_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']], array('OFFERS_SELECTED' => $iOfferSel));
            }
        }

        $arResult['ITEMS'][$iItemKey]['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult['ITEMS'][$iItemKey], $params, 1);

        // compare URL fix
        if ($arParams['DISPLAY_COMPARE']) {
            $arResult['ITEMS'][$iItemKey]['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD_TO_COMPARE_LIST&id='.$arItem['ID'], array('action', 'id', 'ajaxpages', 'ajaxpagesid')));
        }
        // /compare URL fix
    }
}

// ADD AJAX URL
$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('', array('ajaxpages', 'ajaxpagesid', 'get', 'AJAX_CALL', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));

if (is_array($arSectionFilter) && count($arSectionFilter)) {
    $this->__component->arResult['CATALOG_SECTION_FILTER'] = $arSectionFilter;
    $this->__component->SetResultCacheKeys(array('CATALOG_SECTION_FILTER'));
}
