<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
$arItems = array();
$aParentItem = false;

foreach ($arResult as $arLinks) {
    if ($arLinks['PARAMS']["DEPTH_LEVEL"] == 3) {
        $arLinks['DEPTH_LEVEL'] = 3;
    }

    if ($arLinks["DEPTH_LEVEL"] == 1) {
        if ($aParentItem && ($aParentItem["PERMISSION"] !== "D")) {
            $arItems[] = $aParentItem;
            $aParentItem = $arLinks;
        } else {
            $aParentItem = $arLinks;
        }
    } else if (($arLinks["DEPTH_LEVEL"] == 2) && ($aParentItem["PERMISSION"] !== "D")) {
        if ($arLinks['PARAMS']['SECTION_ID']) {
            $aParentItem['PARAMS'][$arLinks['PARAMS']['SECTION_ID']] = $arLinks;
        } else {
            $aParentItem['PARAMS'][] = $arLinks;
        }
    } else if (($arLinks["DEPTH_LEVEL"] == 3) && ($aParentItem["PERMISSION"] !== "D")) {
        if ($arLinks['PARAMS']['IBLOCK_SECTION_ID']) {
            $aParentItem['PARAMS'][$arLinks['PARAMS']['IBLOCK_SECTION_ID']]['PARAMS'][] = $arLinks;
        }
    }
}

$arItems[] = $aParentItem;
$arResult = $arItems;
?>