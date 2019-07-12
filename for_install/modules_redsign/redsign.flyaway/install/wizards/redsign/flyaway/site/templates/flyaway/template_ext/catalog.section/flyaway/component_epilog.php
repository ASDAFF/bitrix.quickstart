<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams['IS_AJAX'] == "Y") {
    $APPLICATION->RestartBuffer();
    $templateData[$arParams['AJAX_ID_SECTION'].'_sorter'] = $APPLICATION->GetViewContent($arParams['AJAX_ID_SECTION'].'_sorter');
    if (SITE_CHARSET != 'utf-8') {
        $arJson = array(
            'HTMLBYID' => $templateData,
        );
        $data = $APPLICATION->ConvertCharsetArray($arJson, SITE_CHARSET, 'utf-8');
        $json_str_utf = json_encode($data);
        $json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
        echo $json_str;
    } else {
        echo json_encode($arJson);
    }
    die();
}
if (is_array($arResult["CATALOG_SECTION_FILTER"]) && count($arResult["CATALOG_SECTION_FILTER"])) {
    global $arSectionFilter;
    $arSectionFilter = $arResult["CATALOG_SECTION_FILTER"];
}
