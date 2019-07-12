<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(
    !Loader::includeModule('redsign.devfunc') ||
    !Loader::includeModule('iblock')
) {
    return true;
}


$maxWidthSize = 300;
$maxHeightSize = 300;
$params = array(
	'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
	'MAX_WIDTH' => $maxWidthSize,
	'MAX_HEIGHT' => $maxHeightSize,
);
RSDevFunc::GetDataForProductItem($arResult['ITEMS'], $params);
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$maxWidthSize,'MAX_HEIGHT'=>$maxHeightSize));


$arResult['SECTIONS'] = array();
$arSectionIds = array();
foreach($arResult['ITEMS'] as &$arItem) {
    if(!is_array($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])) {
        $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = array();
        $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ITEMS'] = array();
    }

    $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = &$arItem;
    $arSectionIds[] = $arItem['IBLOCK_SECTION_ID'];

}
unset($arItem);
$dbSections = CIBLockSection::GetTreeList(array(
    'ID' => $arSectionIds
));
while($arSection = $dbSections->GetNext()) {
    $arResult['SECTIONS'][$arSection['ID']]["NAME"] = $arSection['NAME'];
}
