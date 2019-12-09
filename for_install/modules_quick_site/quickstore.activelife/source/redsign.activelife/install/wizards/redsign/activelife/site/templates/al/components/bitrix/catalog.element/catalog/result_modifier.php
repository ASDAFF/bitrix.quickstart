<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\ModuleManager;
use Bitrix\Iblock;

if (
    !Loader::includeModule('redsign.activelife') ||
    !Loader::includeModule('redsign.devfunc')
) {
    return;
}

$arResult['OFFERS_IBLOCK_ID'] = 0;
$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
if (!empty($arSKU) && is_array($arSKU)) {
    $arResult['OFFERS_IBLOCK_ID'] = $arSKU['IBLOCK_ID'];
}

if ($arParams['ICON_MEN_PROP'] != '' && $arParams['ICON_MEN_PROP'] != '-') {
	$arParams['ICON_MEN_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP']);
} else {
    $arParams['ICON_MEN_PROP'] = array();
}

if ($arParams['ICON_WOMEN_PROP'] != '' && $arParams['ICON_WOMEN_PROP'] != '-') {
	$arParams['ICON_WOMEN_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP']);
} else {
    $arParams['ICON_WOMEN_PROP'] = array();
}

if ($arParams['ICON_NOVELTY_PROP'] != '' && $arParams['ICON_NOVELTY_PROP'] != '-') {
    $arParams['ICON_NOVELTY_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP']);
} else {
    $arParams['ICON_NOVELTY_PROP'] = array();
}

if ($arParams['ICON_DEALS_PROP'] != '' && $arParams['ICON_DEALS_PROP'] != '-') {
    $arParams['ICON_DEALS_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP']);
} else {
    $arParams['ICON_DEALS_PROP'] = array();
}

if ($arParams['ICON_DISCOUNT_PROP'] != '' && $arParams['ICON_DISCOUNT_PROP'] != '-') {
    $arParams['ICON_DISCOUNT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP']);
} else {
    $arParams['ICON_DISCOUNT_PROP'] = array();
}

if ($arParams['ADDITIONAL_PICT_PROP'] != '' && $arParams['ADDITIONAL_PICT_PROP'] != '-') {
    $arParams['ADDITIONAL_PICT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP']);
} else {
    $arParams['ADDITIONAL_PICT_PROP'] = array();
}

if ($arParams['BRAND_PROP'] != '' && $arParams['BRAND_PROP'] != '-') {
    $arParams['BRAND_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['BRAND_PROP']);
} else {
    $arParams['BRAND_PROP'] = array();
}

if ($arParams['ARTICLE_PROP'] != '' && $arParams['ARTICLE_PROP'] != '-') {
    $arParams['ARTICLE_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ARTICLE_PROP']);
} else {
    $arParams['ARTICLE_PROP'] = array();
}

if ($arParams['LIKES_COUNT_PROP'] !== '' || $arParams['LIKES_COUNT_PROP'] == '-') {
    $arParams['LIKES_COUNT_PROP'] = Option::get('redsign.activelife', 'propcode_likes', 'LIKES_COUNT');
}

if ($arResult['OFFERS_IBLOCK_ID']) {
    if ($arParams['OFFER_ADDITIONAL_PICT_PROP'] != '' && $arParams['OFFER_ADDITIONAL_PICT_PROP'] != '-') {
        $arParams['ADDITIONAL_PICT_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
    }
    if ($arParams['OFFER_ARTICLE_PROP'] != '' && $arParams['OFFER_ARTICLE_PROP'] != '-') {
        $arParams['ARTICLE_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ARTICLE_PROP'];
    }
    if (is_array($arParams['OFFER_TREE_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_PROPS'];
        unset($arParams['OFFER_TREE_PROPS']);
        $arParams['OFFER_TREE_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
    }
    if (is_array($arParams['OFFER_TREE_COLOR_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_COLOR_PROPS'];
        unset($arParams['OFFER_TREE_COLOR_PROPS']);
        $arParams['OFFER_TREE_COLOR_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
    }
    if (is_array($arParams['OFFER_TREE_BTN_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_BTN_PROPS'];
        unset($arParams['OFFER_TREE_BTN_PROPS']);
        $arParams['OFFER_TREE_BTN_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
    }

    $arCahceOffers = array();
}

$arElementsIDs = array(
    $arResult['ID'] => 0
);

if (is_array($arResult['OFFERS']) && count($arResult['OFFERS']) > 0) {

    $arResult['OFFERS_SELECTED'] = !empty($arResult['OFFERS_SELECTED']) ? $arResult['OFFERS_SELECTED'] : 0;

    foreach ($arResult['OFFERS'] as $iOfferKey => $arOffer) {

        // USE_PRICE_COUNT fix
        //if (!in_array($arOffer['ID'], $arElementsIDs)) {
        if (!isset($arElementsIDs[$arOffer['ID']])) {

            $arElementsIDs[$arOffer['ID']] = $iOfferKey;

        } else {

            if (isset($arResult['OFFERS_SELECTED']) && $arResult['OFFERS_SELECTED'] == $iOfferKey) {
                $arResult['OFFERS_SELECTED'] = $arElementsIDs[$arOffer['ID']];
            }
            unset($arResult['OFFERS'][$iOfferKey]);
            continue;

        }

        //fix OFFERS_SELECTED
        if (
            $arResult['OFFERS_SELECTED'] == 0 &&
            strlen($arParams['OFFERS_SELECTED']) > 0 &&
            (
                $arParams['OFFERS_SELECTED'] == $arOffer['CODE'] ||
                $arParams['OFFERS_SELECTED'] == $arOffer['ID']
            )
        ) {
            $arResult['OFFERS_SELECTED'] = $iOfferKey;
            //break;
        }
    }
}

if ($arParams['USE_PRICE_COUNT']) {

    $arPriceTypeID = array();
    foreach ($arResult['CAT_PRICES'] as $value) {
        $arPriceTypeID[] = $value['ID'];
    }
}

$params = array(
    'RESIZE' => array(
        'small' => array(
            'MAX_WIDTH' => 90,
            'MAX_HEIGHT' => 90,
        ),
        'big' => array(
            'MAX_WIDTH' => 550,
            'MAX_HEIGHT' => 500,
        ),
    ),
    'DETAIL_PICTURE' => true,
    'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
    'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
    'FILTER_PRICE_TYPES' => $arPriceTypeID,
    'CURRENCY_PARAMS' => $arResult['CONVERT_CURRENCY'],
);

// get other data
$arItems = array(0 => &$arResult);
RSDevFunc::GetDataForProductItem($arItems, $params);

RSDevFunc::getElementPictures($arResult, $params);

if (is_array($arResult['OFFERS']) && count($arResult['OFFERS']) > 0) {

    $arResult['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties(
        $arResult['OFFERS'],
        $arParams['OFFER_TREE_PROPS'][$arResult['OFFERS_IBLOCK_ID']],
        array(
            'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED']
        )
    );

    $bNeedColors = false;
    if (is_array($arParams['OFFER_TREE_COLOR_PROPS'][$arResult['OFFERS_IBLOCK_ID']])) {
        foreach ($arParams['OFFER_TREE_COLOR_PROPS'][$arResult['OFFERS_IBLOCK_ID']] as $sPropCode) {
            // foreach($arColorProps as  $sPropCode)
            // {
                if (
                    isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['PROPERTIES'][$sPropCode]) &&
                    (
                        $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] != 'S' ||
                        $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['PROPERTIES'][$sPropCode]['USER_TYPE'] != 'directory'
                    )
                ) {
                    $bNeedColors = true;
                    break;
                }
            // }
        }
    }

    if ($bNeedColors) {
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

    // get size table
    $arResult['SIZE_TABLE'] = '';
    if (
        !empty($arParams['OFFER_TREE_BTN_PROPS'][$arResult['OFFERS_IBLOCK_ID']]) &&
        $arParams['SIZE_TABLE_USER_FIELD_CODE'] != ''
    ) {
        $bNeedSizes = false;
        foreach ($arParams['OFFER_TREE_BTN_PROPS'][$arResult['OFFERS_IBLOCK_ID']] as $sPropCode) {
            if (is_array($arResult['OFFERS_EXT']['PROPERTIES'][$sPropCode]) && count($arResult['OFFERS_EXT']['PROPERTIES'][$sPropCode])) {
                $bNeedSizes = true;
                break;
            }
        }

        if ($bNeedSizes) {
            $arrPath = array();
            $arrIDs = array();
            $resPath = CIBlockSection::GetNavChain($arResult['IBLOCK_ID'], $arResult['IBLOCK_SECTION_ID']);
            while ($arSec = $resPath->GetNext()){
                $arrIDs[] = $arSec['ID'];
            }
            $arFilter = array('IBLOCK_ID' => $arResult['IBLOCK_ID'], 'ID' => $arrIDs, 'GLOBAL_ACTIVE' => 'Y');
            $db_list = CIBlockSection::GetList(array('DEPTH_LEVEL'=>'DESC'), $arFilter, false, array($arParams['SIZE_TABLE_USER_FIELD_CODE']));
            while ($arrValue = $db_list->GetNext()) {
                if ($arrValue[$arParams['SIZE_TABLE_USER_FIELD_CODE']] != '') {
                    $arrPath[] = $arrValue;
                    $arResult['SIZE_TABLE'] = $arrValue['~'.$arParams['SIZE_TABLE_USER_FIELD_CODE']];
                    break;
                }
            }
        }
    }
    
    foreach ($arResult['OFFERS'] as $iOfferKey => $arOffer) {

        $arCahceOffers[$iOfferKey] = array(
            'ID' => $arOffer['ID'],
            'IBLOCK_ID' => $arOffer['IBLOCK_ID'],
            'HAVE_SET' => $arOffer['HAVE_SET'],
        );
    }

} else {

    $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);

    if ($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y' && !$emptyProductProperties) {

        $arPropList = array();
        $arNeedValues = array();

        $arPropList = array();
        foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo) {
            if (
                $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE'] == 'S' &&
                $arResult['PROPERTIES'][$propID]['USER_TYPE'] == 'directory'
            ) {

                $arPropList[$propID] = $arResult['PROPERTIES'][$propID];
                RSDevFunc::getHighloadBlockValues($arPropList[$propID]);

                foreach ($propInfo['VALUES'] as $sPropCode => $spropVal) {
                    if (is_array($arPropList[$propID]['VALUES']) && count($arPropList[$propID]['VALUES']) > 0) {
                        foreach ($arPropList[$propID]['VALUES'] as $sPropId => $arPropVal) {
                            if ($sPropCode == $arPropVal['XML_ID']) {
                                $arResult['PRODUCT_PROPERTIES'][$propID]['VALUES'][$sPropCode] = $arPropVal;

                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}

if (
    isset($arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]) &&
    $arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]['PROPERTY_TYPE'] == 'S' &&
    $arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]['USER_TYPE'] == 'directory'
) {
    $rsBrand = CIBlockElement::GetList(
        array(),
        array(
            'IBLOCK_ID' => $arParams['BRAND_IBLOCK_ID'],
            'PROPERTY_'.$arParams['BRAND_IBLOCK_BRAND_PROP'] => $arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]['VALUE']
        ),
        false,
        false,
        array(
            'NAME',
            'PREVIEW_PICTURE',
            'PROPERTY_'.$arParams['BRAND_IBLOCK_BRAND_PROP']
        )
    );

    if ($arBrand = $rsBrand->GetNext()) {
        $arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]['ALT'] = $arBrand['NAME'];
        if (intval($arBrand['PREVIEW_PICTURE']) > 0) {
            $arResult['PROPERTIES'][$arParams['BRAND_PROP'][$arResult['IBLOCK_ID']]]['PICT']['SRC'] = CFile::GetPath($arBrand['PREVIEW_PICTURE']);
        }
    }
} else if (
    isset($arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP']]['VALUE']) &&
    intval($arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP']]['VALUE']) > 0
) {
    /* old brands */
    $arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP']]['PICT']['SRC'] = CFile::GetPath($arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP']]['VALUE']);
}

$arFilterProps = array(
    $arParams['BRAND_PROP'],
);

foreach ($arFilterProps as $arProps) {
    $sPropCode = $arProps[$arResult['IBLOCK_ID']];

    if (isset($arResult['PROPERTIES'][$sPropCode])) {
        if (is_array($arResult['PROPERTIES'][$sPropCode]['VALUE'])) {
            foreach ($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $iPropValue => $sPropValue) {
                $arResult['PROPERTIES'][$sPropCode]['FILTER_URL'][] = $arResult['LIST_PAGE_URL']
                    .(strpos($arResult['LIST_PAGE_URL'], '?') === false ? '?' : '').$arParams['FILTER_NAME'].'_'
                    .$arResult['PROPERTIES'][$sPropCode]['ID'].'_'
                    .abs(crc32($arResult['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'][$iPropValue]
                        ? $arResult['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'][$iPropValue]
                        : htmlspecialcharsbx($sPropValue))
                    ).'=Y&set_filter=Y';
            }
        } else {
            $arResult['PROPERTIES'][$sPropCode]['FILTER_URL'] = $arResult['LIST_PAGE_URL']
                .(strpos($arResult['LIST_PAGE_URL'], '?') === false ? '?' : '').$arParams['FILTER_NAME'].'_'
                .$arResult['PROPERTIES'][$sPropCode]['ID'].'_'
                .abs(crc32($arResult['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID']
                    ? $arResult['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID']
                    : htmlspecialcharsbx($arResult['PROPERTIES'][$sPropCode]['VALUE']))
                ).'=Y&set_filter=Y';
        }
    }
}

// PREPARE DOCS
if (is_array($arParams['TAB_IBLOCK_PROPS']) && count($arParams['TAB_IBLOCK_PROPS']) > 0) {
    foreach ($arParams['TAB_IBLOCK_PROPS'] as $iPropKey => $sPropCode) {
        if ($sPropCode != '' && $arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'F') {
            if (is_array($arResult['PROPERTIES'][$sPropCode]['VALUE'])) {
                foreach ($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $iPropValKey => $iPropVal) {
                    $rsFile = CFile::GetByID($iPropVal);
                    if ($arFile = $rsFile->Fetch()) {
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][$iPropValKey] = $arFile;
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][$iPropValKey]['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
                        $tmp = explode('.', $arFile['FILE_NAME']);
                        $tmp = end($tmp);
                        $type = 'other';
                        $type2 = '';
                        switch ($tmp) {
                            case 'docx':
                                $type = 'doc';
                                $type2 = 'WORD';
                                break;
                            case 'doc':
                                $type = 'doc';
                                $type2 = 'WORD';
                                break;
                            case 'pdf':
                                $type = 'pdf';
                                $type2 = 'PDF';
                                break;
                            case 'xml':
                                $type = 'excel';
                                $type2 = 'EXCEL';
                                break;
                            case 'xlsx':
                                $type = 'excel';
                                $type2 = 'EXCEL';
                                break;
                        }
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][$iPropValKey]['TYPE'] = $type;
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][$iPropValKey]['TYPE2'] = $type2;
                        $arResult['PROPERTIES'][$sPropCode]['VALUE'][$iPropValKey]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
                    }
                }
            }
        }
    }
}

$arResult['TABS'] = array(
    'DETAIL_TEXT' => false,
    'DISPLAY_PROPERIES' => false,
    'TAB_PROPERTIES' => false,
    'REVIEW' => false,
);

if ($arResult['DETAIL_TEXT'] != '') {
    $arResult['TABS']['DETAIL_TEXT'] = true;
}

if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0) {
    $arResult['TABS']['DISPLAY_PROPERIES'] = true;
}

if (is_array($arParams['TAB_IBLOCK_PROPS']) && count($arParams['TAB_IBLOCK_PROPS']) > 0) {
    foreach ($arParams['TAB_IBLOCK_PROPS'] as $sPropCode) {
        if ($sPropCode != '' && !empty($arResult['PROPERTIES'][$sPropCode]['VALUE'])) {
            $arResult['TABS']['TAB_PROPERTIES'] = true;
            break;
        }
    }
}

if ($arParams['USE_REVIEW'] == 'Y' && ModuleManager::isModuleInstalled('forum')) {
    $arResult['TABS']['REVIEW'] = true;
}


$this->__component->arResult['TABS'] = $arResult['TABS'];
if ($arResult['OFFERS_IBLOCK_ID']) {
    $this->__component->arResult['OFFERS_IBLOCK_ID'] = $arResult['OFFERS_IBLOCK_ID'];
    $this->__component->arResult['OFFERS_CACHE'] = $arCahceOffers;
}

$this->__component->SetResultCacheKeys(array('TABS', 'OFFERS_IBLOCK_ID', 'OFFERS_CACHE', 'HAVE_SET'));