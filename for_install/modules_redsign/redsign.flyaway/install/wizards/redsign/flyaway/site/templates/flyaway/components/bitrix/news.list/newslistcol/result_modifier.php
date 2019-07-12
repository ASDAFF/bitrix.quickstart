<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) {
    die();
}

if(!empty($arResult['ITEMS']) && is_array($arResult['ITEMS'])) {
    foreach($arResult['ITEMS'] as &$arItem) {

        if(!empty($arItem['PREVIEW_PICTURE']) && !empty($arItem['PREVIEW_PICTURE']['EXTERNAL_ID'])) {

            $arItem['PREVIEW_PICTURE']['RESIZE'] = CFile::ResizeImageGet(
                $arItem['PREVIEW_PICTURE']['ID'],
                array(
                    "width" => 100,
                    "height" => 75
                ),
                false,
                false,
                60
            );

        }

    }
    unset($arItem);
}
