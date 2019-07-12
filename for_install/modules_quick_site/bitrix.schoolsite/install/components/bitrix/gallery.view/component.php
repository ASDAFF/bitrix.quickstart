<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("iblock")) {
    ShowError('Module Iblock not installed');
}

if ($arParams['IBLOCK_ID'] == 0) {
    ShowError('IBLOCK_ID is empty');
    return;
}

$arParams['SORT_BY1'] = trim($arParams['SORT_BY1']);

if(empty($arParams['SORT_BY1'])) {
    $arParams["SORT_BY1"] = 'ACTIVE_FROM';
}

if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams['SORT_ORDER1'])) {
    $arParams['SORT_ORDER1'] = 'DESC';
}

if (empty($arParams['SORT_BY2'])) {
    $arParams['SORT_BY2'] = 'SORT';
}
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams['SORT_ORDER2'])) {
    $arParams['SORT_ORDER2'] = 'ASC';
}

if (empty($arParams['FILTER_NAME']) || !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME'])) {
    $arrFilter = array();
} else {
    $arrFilter = $GLOBALS[$arParams['FILTER_NAME']];

    if (!is_array($arrFilter)) {
        $arrFilter = array();
    }
}

$arResult = array();

if ($this->StartResultCache(false, $arrFilter)) {
    if (!is_array($arParams['~ITEMS'])) {
        $arSort = array(
            $arParams['SORT_BY1'] => $arParams['SORT_ORDER1'],
            $arParams['SORT_BY2'] => $arParams['SORT_ORDER2'],
        );

        $arFilter = array(
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
        );

        if (!empty($arParams['SECTION_ID'])) {
            $arFilter['SECTION_ID'] = $arParams['SECTION_ID'];
        }

        $arFilter = array_merge($arFilter, $arrFilter);
        $arSelect = array('ID', 'NAME', 'PREVIEW_PICTURE', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL');

        $resPhotos = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    } else {
        $resPhotos = new CDBResult;
        $resPhotos->InitFromArray($arParams['~ITEMS']);
    }

    $arResult['ITEMS'] = array();

    while ($arItem = $resPhotos->GetNext()) {
        $arItem['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
        $arItem['DETAIL_PAGE_URL'] = str_replace('#GALLERY_ID#', $arParams['GALLERY_ID'], $arItem['DETAIL_PAGE_URL']);
        $arResult['ITEMS'][] = $arItem;
    }

    $this->IncludeComponentTemplate();
}

if(!empty($arParams['GALLERY_CSS'])) {
    $APPLICATION->SetAdditionalCss($arParams['GALLERY_CSS']);
}

?>
