<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

$arParams['CATALOG_IBLOCK_ID'] = intval($arParams['CATALOG_IBLOCK_ID']);

if (!isset($arParams['CATALOG_BRAND_PROP']) || strlen($arParams['CATALOG_BRAND_PROP']) <= 0) {
    $arParams['CATALOG_BRAND_PROP'] = false;
}

if (intval($arParams['CATALOG_IBLOCK_ID']) > 0 && strlen($arParams['CATALOG_BRAND_PROP']) > 0) {

    $propRes = CIBlockProperty::GetList(
        array(
            'SORT' => 'ASC',
            'ID' => 'DESC'
        ),
        array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['CATALOG_IBLOCK_ID'],
            'CODE' => $arParams['CATALOG_BRAND_PROP']
        )
    );
    
    if ($arFields = $propRes->GetNext()) {
        $arResult['CATALOG_BRAND_PROP'] = $arFields;
    }
    
    $arFilterProps = array(
        $arParams['BRAND_PROP']
    );

    foreach ($arResult['ITEMS'] as $iItemKey => $arItem) {

        foreach ($arFilterProps as $sPropCode) {

            if ($sPropCode != '' && isset($arItem['PROPERTIES'][$sPropCode])) {

                if (is_array($arItem['PROPERTIES'][$sPropCode]['VALUE'])) {
                    
                    foreach ($arItem['PROPERTIES'][$sPropCode]['VALUE'] as $iPropValue => $sPropValue) {

                        $arResult['ITEMS'][$iItemKey]['DETAIL_PAGE_URL'] = getFilterUrl(
                            array(
                                'SEF_MODE' => $arParams['SEF_CATALOG'],
                                'BRAND_URL' => $arParams['DETAIL_URL'],
                                'CATALOG_FILTER_NAME' => $arParams['CATALOG_FILTER_NAME'],
                            ),
                            array(
                                'ID' => $arResult['CATALOG_BRAND_PROP']['ID'],
                                'CODE' => $arResult['CATALOG_BRAND_PROP']['CODE'],
                                'VALUE_ENUM_ID' => $arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'][$iPropValue],
                                'VALUE' => $sPropValue,
                            )
                        );
                    }

                } else {

                    $arResult['ITEMS'][$iItemKey]['DETAIL_PAGE_URL'] = getFilterUrl(
                        array(
                            'SEF_MODE' => $arParams['SEF_CATALOG'],
                            'BRAND_URL' => $arParams['DETAIL_URL'],
                            'CATALOG_FILTER_NAME' => $arParams['CATALOG_FILTER_NAME'],
                        ),
                        array(
                            'ID' => $arResult['CATALOG_BRAND_PROP']['ID'],
                            'CODE' => $arResult['CATALOG_BRAND_PROP']['CODE'],
                            'VALUE_ENUM_ID' => $arItem['PROPERTIES'][$sPropCode]['VALUE_ENUM_ID'],
                            'VALUE' => $arItem['PROPERTIES'][$sPropCode]['VALUE'],
                        )
                    );

                }
            }
        }
    }
}



/**
 * Makes url for catalog.smart.filter.
 *
 * @param array $arParams
 * @param array $arProp
 * @return bool
 */

function getFilterUrl ($arParams, $arProp) {

    /*
    $arParams = array(
        'SEF_MODE' => 'Y',
        'BRAND_URL' => '',
        'CATALOG_FILTER_NAME' => '',
    );
    $arProp = array(
        'ID' => 0,
        'VALUE_ENUM_ID' => '',
        'VALUE' => '',
    );
    */
    if ($arParams['SEF_MODE'] == 'Y') {
        
        CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");
        
        $filter = new CBitrixCatalogSmartFilter();
        
        $filter->SAFE_FILTER_NAME = $arParams['CATALOG_FILTER_NAME'];
        
        $smartParts = $smartPart = array();
        
        $smartPart[] = toLower($arProp['VALUE_ENUM_ID'] ? $arProp['VALUE_ENUM_ID'] : $arProp['VALUE']);
        
        if ($arProp["CODE"]) {
            array_unshift($smartPart, toLower($arProp["CODE"]));
        } else {
            array_unshift($smartPart, $arProp["ID"]);
        }
        
        $smartParts[] = $smartPart;
        
        return str_replace("#SMART_FILTER_PATH#", implode("/", $filter->encodeSmartParts($smartParts)), $arParams['BRAND_URL']);
        
    } else {
        $paramsToDelete = array("set_filter", "del_filter", "ajax", "bxajaxid", "AJAX_CALL", "mode");
        $clearURL = CHTTP::urlDeleteParams($arParams['BRAND_URL'], $paramsToDelete, array("delete_system_params" => true));
        
        $paramsToAdd = array(
            $arParams['CATALOG_FILTER_NAME'].'_'.$arProp['ID'].'_'.abs(crc32(
                $arProp['VALUE_ENUM_ID'] ? $arProp['VALUE_ENUM_ID'] : $arProp['VALUE']
            )) => 'Y',
            'set_filter' => 'Y'
        );
        
        return htmlspecialcharsbx(
            CHTTP::urlAddParams($clearURL, $paramsToAdd, array(
                "skip_empty" => true,
                "encode" => true,
            ))
        );        
    }
    
}
