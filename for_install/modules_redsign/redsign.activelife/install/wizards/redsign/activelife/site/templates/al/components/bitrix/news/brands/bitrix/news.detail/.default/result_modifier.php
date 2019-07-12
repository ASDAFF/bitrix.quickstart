<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

use \Bitrix\Main\Loader;

if (!Loader::includeModule('iblock')) {
    return;
}

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
    if (!empty($arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'])) {

        $arSectionFilter = array();
        $dbElement = CiblockElement::getList(
            array(),
            array(
                'ACTIVE' => 'Y',
                'GLOBAL_ACTIVE' => 'Y',
                'IBLOCK_ID' => $arParams['CATALOG_IBLOCK_ID'],
                'PROPERTY_'.$arResult['CATALOG_BRAND_PROP']['ID'].'_VALUE' => $arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE']
            ),
            array(
                'IBLOCK_SECTION_ID'
            ),
            false,
            array(
                'PROPERTY_'.$arResult['CATALOG_BRAND_PROP']['ID'],
            )
        );
        
        while ($arElement = $dbElement->GetNext()) {      
            if (!in_array($arElement['IBLOCK_SECTION_ID'], $arSectionFilter)) {
                $arSectionFilter[] = $arElement['IBLOCK_SECTION_ID'];
            }
        }
        
        if (is_array($arSectionFilter) && count($arSectionFilter) > 0) {
            $this->__component->arResult['CATALOG_SECTION_FILTER'] = $arSectionFilter;
        }
    }
}

if (!empty($arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'])) {
    $arResult['CATALOG_FILTER'] = array();

    $filterKey = '=PROPERTY_'.$arResult['CATALOG_BRAND_PROP']['ID'];

    $arResult['CATALOG_FILTER'][$filterKey] = $arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'];

    $this->__component->arResult['CATALOG_FILTER'] = $arResult['CATALOG_FILTER'];

    if ($arResult['CATALOG_BRAND_PROP']['CODE']) {
        $this->__component->arResult['SMART_FILTER_PATH'] = toLower($arResult['CATALOG_BRAND_PROP']['CODE']).'-is-'.$arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'];
    } else {
        $this->__component->arResult['SMART_FILTER_PATH'] = $arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'].'-is-'.toLower($arResult['CATALOG_BRAND_PROP']['ID']);
    }
}

    $this->__component->SetResultCacheKeys(array('CATALOG_FILTER', 'SMART_FILTER_PATH', 'CATALOG_SECTION_FILTER'));
?>