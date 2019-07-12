<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$FILTER_NAME = (string)$arParams['CATALOG_FILTER_NAME'];

global ${$FILTER_NAME};
if (!is_array(${$FILTER_NAME})) {
	${$FILTER_NAME} = array();
}

${$FILTER_NAME} = array_merge(${$FILTER_NAME}, $arResult['CATALOG_FILTER']);

global $sSmartFilterPath;
if (!empty($arResult['SMART_FILTER_PATH'])) {
    $sSmartFilterPath = $arResult['SMART_FILTER_PATH'];
}

if (is_array($arResult['CATALOG_SECTION_FILTER']) && count($arResult['CATALOG_SECTION_FILTER'])) {
    global $arSectionFilter;
    $arSectionFilter = $arResult['CATALOG_SECTION_FILTER'];
}