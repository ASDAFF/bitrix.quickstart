<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if (!function_exists('recursiveAlignItems')) {
    function recursiveAlignItems(&$arItems, $level = 1, &$i = 0)
    {
        $returnArray = array();

        if (!is_array($arItems)) {
            return $returnArray;
        }

        for (
            $currentItemKey = 0, $countItems = count($arItems);
            $i < $countItems;
            $i++
        ) {
            $arItem = $arItems[$i];

            if ($arItem['DEPTH_LEVEL'] == $level) {
                $returnArray[$currentItemKey++] = $arItem;
            } elseif ($arItem['DEPTH_LEVEL'] > $level) {
                $returnArray[$currentItemKey - 1]['SUB_ITEMS'] = recursiveAlignItems(
                    $arItems,
                    $level + 1,
                    $i
                );
            } elseif ($level > $arItem['DEPTH_LEVEL']) {
                --$i;
                break;
            }
        }

        return $returnArray;
    }
}

$arResult = recursiveAlignItems($arResult);

/** Get menu info **/
if (!Loader::includeModule('iblock')) {
    return;
}

$rootSectionIds = array();
foreach ($arResult as &$rootItem) {
    $rootSectionIds[] = $rootItem['PARAMS']['SECTION_ID'];
}
unset($rootItem);

$arFilter = array(
    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
    "SITE_ID" => SITE_ID,
    "ACTIVE" => "Y"
);

$arSectionsInfo = array();

$obCache = new CPHPCache();
if ($obCache->InitCache(3600, serialize($arFilter), "/iblock/menu")) {
    $vars = $obCache->GetVars();
    $arSectionsInfo = $vars['arSectionsInfo'];
} elseif ($obCache->StartDataCache()) {
    $arUserFieldValues = array();
    $dbUserField = CUserFieldEnum::GetList(array(), array('USER_FIELD_NAME' => 'UF_PICTURE_PLACE'));

    while ($arUserField = $dbUserField->Fetch()) {
        $arUserFieldValues[$arUserField['ID']] = $arUserField;
    }

    if ($arParams['RSFLYAWAY_IS_SHOW_IMAGE'] == "Y") {
        /* Get picture */
        $dbSections = CIBlockSection::GetList(
            array(),
            array(
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'ID' => $rootSectionIds
            ),
            false,
            array(
                'ID',
                'DETAIL_PICTURE',
                'UF_*'
            )
        );

        while ($arSection = $dbSections->GetNext()) {
            $arSectionsInfo[$arSection['ID']] = array(
                'PICTURE' => CFile::GetPath($arSection['DETAIL_PICTURE']),
                'PICTURE_POSITION' => $arUserFieldValues[$arSection['UF_PICTURE_PLACE']]['VALUE'],
                'PICTURE_OFFSET_X' => $arSection['UF_OFFSET_X'],
                'PICTURE_OFFSET_Y' => $arSection['UF_OFFSET_Y']
            );
        }
        /* /Get picture */
    }



    if (defined("BX_COMP_MANAGED_CACHE")) {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->StartTagCache("/iblock/menu");
        $CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
        $CACHE_MANAGER->EndTagCache();
    }

    $obCache->EndDataCache(array(
        "arSectionsInfo" => $arSectionsInfo
    ));
}

foreach ($arResult as &$rootItem) {
    if (!empty($arSectionsInfo[$rootItem['PARAMS']['SECTION_ID']])) {
        $rootItem['PARAMS'] = array_merge($rootItem['PARAMS'], $arSectionsInfo[$rootItem['PARAMS']['SECTION_ID']]);
    }
}
unset($rootItem);
