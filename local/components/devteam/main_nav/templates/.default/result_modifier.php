<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult['IBLOCKS'] as $k => $iblock)
    $arResult['IBLOCKS'][$k]["IBLOCK"]["LIST_PAGE_URL"] = str_replace(
            array('#SITE_DIR#', '#IBLOCK_CODE#'), 
            array('', $iblock["IBLOCK"]['CODE']), 
            $iblock["IBLOCK"]["LIST_PAGE_URL"]);
