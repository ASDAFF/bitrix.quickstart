<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

if (
	!Loader::includeModule('redsign.activelife') ||
	!Loader::includeModule('redsign.devfunc')
) {
	return;
}

$arParams['PREVIEW_TRUNCATE_LEN'] = intval($arParams['PREVIEW_TRUNCATE_LEN']);

if ($arParams['LIKES_COUNT_PROP'] == '') {
	$arParams['LIKES_COUNT_PROP'] = Option::get('redsign.activelife', 'propcode_likes', 'LIKES_COUNT');
}

if ($arParams['POPUP_DETAIL_VARIABLE'] != 'ON_IMAGE' && $arParams['POPUP_DETAIL_VARIABLE'] != 'ON_LUPA') {
	$arParams['POPUP_DETAIL_VARIABLE' ] = 'ON_NONE';
}

if ($arParams['USE_AJAXPAGES'] == 'Y') {
	$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('', array('ajaxpages', 'ajaxpagesid', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));
}

if ($arParams['TEMPLATE_AJAXID'] == '') {
	$arParams['TEMPLATE_AJAXID'] = $this->getEditAreaId('catalog_ajax');
}

if (empty($arParams['CATALOG_FILTER_NAME']) || !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['CATALOG_FILTER_NAME'])) {
	$arParams['CATALOG_FILTER_NAME'] = 'arrFilter';
}

$bNeedColors = false;
$params = array(
	'PREVIEW_PICTURE' => true,
	'DETAIL_PICTURE' => true,
	'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
	'RESIZE' => array(
		0 => array(
			'MAX_WIDTH' => 210,
			'MAX_HEIGHT' => 160,
		)
	)
);

if (!empty($arResult['ITEMS'])) {

    // new catalog components fix
    if (!isset($arResult['PRICES'])) {
        $arResult['PRICES'] = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
    }

    if ($arParams['USE_PRICE_COUNT']) {

        $arPriceTypeID = array();
        foreach ($arResult['PRICES'] as $value) {
            $arPriceTypeID[] = $value['ID'];
        }

        $arElementsIDs = array();
        if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {

            foreach ($arResult['ITEMS'] as $iItemKey => $arItem) {

                $arElementsIDs[$arItem['ID']] = $iItemKey;
                if (is_array($arItem['OFFERS']) && count($arItem['OFFERS']) > 0) {

                    foreach ($arItem['OFFERS'] as $iOfferKey => $arOffer) {

                        // USE_PRICE_COUNT fix
                        //if (!in_array($arOffer['ID'], $arElementsIDs)) {
                        if (!isset($arElementsIDs[$arOffer['ID']])) {
                            $arElementsIDs[$arOffer['ID']] = $iOfferKey;
                        } else {
                          unset($arResult['ITEMS'][$iItemKey]['OFFERS'][$iOfferKey]);
                            if (isset($arItem['OFFERS_SELECTED']) && $arItem['OFFERS_SELECTED'] == $iOfferKey) {
                                $arResult['ITEMS'][$iItemKey]['OFFERS_SELECTED'] = $arElementsIDs[$arOffer['ID']];
                            }
                        }
                    }
                }
            }
        }

        $params['USE_PRICE_COUNT'] = $arParams['USE_PRICE_COUNT'];
        $params['FILTER_PRICE_TYPES'] = $arPriceTypeID;
        $params['CURRENCY_PARAMS'] = $arResult['CONVERT_CURRENCY'];
    }

	RSDevFunc::GetDataForProductItem($arResult['ITEMS'], $params);

	$obParser = new CTextParser;

	foreach ($arResult['ITEMS'] as $iItemKey => $arItem) {
        
		if (is_array($arItem['OFFERS']) && count($arItem['OFFERS']) > 0) {

			$iOfferSel = isset($arItem['OFFERS_SELECTED'])
                ? $arItem['OFFERS_SELECTED']
                : 0;
                
            $arResult['ITEMS'][$iItemKey]['OFFERS_SELECTED'] = $iOfferSel;

			if (!empty($arParams['OFFER_TREE_COLOR_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']])) {
				foreach ($arParams['OFFER_TREE_COLOR_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']] as $sPropCode) {
					if (
                        isset($arItem['OFFERS'][$iOfferSel]['PROPERTIES'][$sPropCode]) &&
                        (
                            $arItem['OFFERS'][$iOfferSel]['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] != 'S' ||
                            $arItem['OFFERS'][$iOfferSel]['PROPERTIES'][$sPropCode]['USER_TYPE'] != 'directory'
                        )
					) {
						$bNeedColors = true;
						break;
					}
				}
			}

			if (!empty($arParams['OFFER_TREE_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']])) {

				$arResult['ITEMS'][$iItemKey]['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties(
					$arItem['OFFERS'],
					$arParams['OFFER_TREE_PROPS'][$arItem['OFFERS'][$iOfferSel]['IBLOCK_ID']],
					array('OFFERS_SELECTED' => $iOfferSel)
				);
			}
		} else {

            if ($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y'  && !empty($arParams["PRODUCT_PROPERTIES"])) {

                $arPropList = array();
                $arNeedValues = array();

                $arPropList = array();
                $arPropGroupList = array(
                );
                foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo) {
                    if (
                        $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE'] == 'S' &&
                        $arItem['PROPERTIES'][$propID]['USER_TYPE'] == 'directory'
                    ) {

                        $arPropList[$propID] = $arItem['PROPERTIES'][$propID];

                        RSDevFunc::getHighloadBlockValues($arPropList[$propID]);

                        foreach ($propInfo['VALUES'] as $sPropCode => $spropVal) {
                            if (is_array($arPropList[$propID]['VALUES']) && count($arPropList[$propID]['VALUES']) > 0) {
                                foreach ($arPropList[$propID]['VALUES'] as $sPropId => $arPropVal) {
                                    if ($sPropCode == $arPropVal['XML_ID']) {

                                        $arResult['ITEMS'][$iItemKey]['PRODUCT_PROPERTIES'][$propID]['VALUES'][$sPropCode] = $arPropVal;

                                        if (!isset($arResult['ITEMS'][$iItemKey]['PRODUCT_PROPERTIES'][$propID]['GROUPS'])) {
                                            $arResult['ITEMS'][$iItemKey]['PRODUCT_PROPERTIES'][$propID]['GROUPS'] = array(
                                                'DEFAULT' => array()
                                            );
                                        }

                                        if (strlen($arPropVal['DESCRIPTION']) <= 0) {
                                            $arResult['ITEMS'][$iItemKey]['PRODUCT_PROPERTIES'][$propID]['GROUPS']['DEFAULT'][] = $sPropCode;
                                        }
                                        else {
                                            $arResult['ITEMS'][$iItemKey]['PRODUCT_PROPERTIES'][$propID]['GROUPS'][$arPropVal['DESCRIPTION']][] = $sPropCode;
                                        }

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

		if ($arParams['PREVIEW_TRUNCATE_LEN'] > 0) {
			$arResult['ITEMS'][$iItemKey]['PREVIEW_TEXT'] = $obParser->html_cut($arItem['PREVIEW_TEXT'], $arParams['PREVIEW_TRUNCATE_LEN']);
		}

		$arResult['ITEMS'][$iItemKey]['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult['ITEMS'][$iItemKey], $params, 1);

        $arFilterProps = array(
            $arParams['BRAND_PROP'][$arItem['IBLOCK_ID']]
        );

        if (!isset($arResult['LIST_PAGE_URL'])) {
            if (!empty($arParams['SECTIONS_URL'])) {
                $arResult['LIST_PAGE_URL'] = $arParams['SECTIONS_URL'];
            } else if (!empty($arParams['SECTION_URL'])) {
                $arResult['LIST_PAGE_URL'] = $arParams['SECTION_URL'];
            }
        }

		foreach ($arFilterProps as $sPropCode) {
            if ($sPropCode != '' && isset($arItem['PROPERTIES'][$sPropCode])) {
                if (is_array($arItem['PROPERTIES'][$sPropCode]['VALUE'])) {
                    foreach ($arItem['PROPERTIES'][$sPropCode]['VALUE'] as $iPropValue => $sPropValue) {
                        $arResult['ITEMS'][$iItemKey]['PROPERTIES'][$sPropCode]['FILTER_URL'][] = $arResult['LIST_PAGE_URL']
                            .(strpos($arResult['LIST_PAGE_URL'], '?') === false ? '?' : '').$arParams['CATALOG_FILTER_NAME'].'_'
                            .$arItem['PROPERTIES'][$sPropCode]['ID'].'_'
                            .abs(crc32($arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'][$iPropValue]
                                ? $arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'][$iPropValue]
                                : htmlspecialcharsbx($sPropValue)))
                            .'=Y&set_filter=Y';
                    }
                } else {
                    $arResult['ITEMS'][$iItemKey]['PROPERTIES'][$sPropCode]['FILTER_URL'] = $arResult['LIST_PAGE_URL']
                        .(strpos($arResult['LIST_PAGE_URL'], '?') === false ? '?' : '').$arParams['CATALOG_FILTER_NAME'].'_'
                        .$arItem['PROPERTIES'][$sPropCode]['ID'].'_'
                        .abs(crc32($arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID']
                            ? $arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID']
                            : htmlspecialcharsbx($arItem['PROPERTIES'][$sPropCode]['VALUE'])))
                        .'=Y&set_filter=Y';
                }
			}
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
}