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
}

if (!empty($arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE'])) {
    $arResult['CATALOG_FILTER'] = array();
    
    $filterKey = '=PROPERTY_'.$arResult['CATALOG_BRAND_PROP']['ID'];

    $arResult['CATALOG_FILTER'][$filterKey] = array(
        $arResult['PROPERTIES'][$arParams['BRAND_PROP']]['VALUE']
    );
    
    $this->__component->arResult['CATALOG_FILTER'] = $arResult['CATALOG_FILTER']; 
}

$picture = null;

if (!empty($arResult['DETAIL_PICTURE'])) {
    
    if (!is_array($arResult['DETAIL_PICTURE']) && intval($arResult['DETAIL_PICTURE']) > 0) {
        $arResult['DETAIL_PICTURE'] = CFile::GetFileArray($arItem['DETAIL_PICTURE']);
    }
    if (is_array($arResult['DETAIL_PICTURE'])) {
        $arResult['DETAIL_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], Array('width' => 250, 'height' => 250));
    }
    $this->__component->arResult['BRAND_LOGO'] = $arResult['DETAIL_PICTURE'];
    
} else if (!empty($arResult['PREVIEW_PICTURE'])) {
    
    
    if (!is_array($arResult['PREVIEW_PICTURE']) && intval($arResult['PREVIEW_PICTURE']) > 0) {
        $arResult['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
    }
    if (is_array($arResult['PREVIEW_PICTURE'])) {
        $arResult['PREVIEW_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE'], Array('width' => 250, 'height' => 250));
    }
    $this->__component->arResult['BRAND_LOGO'] = $arResult['PREVIEW_PICTURE'];
}

$this->__component->SetResultCacheKeys(array('CATALOG_FILTER', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
