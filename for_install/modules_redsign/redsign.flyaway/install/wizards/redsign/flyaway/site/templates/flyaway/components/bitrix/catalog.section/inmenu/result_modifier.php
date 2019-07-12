<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
/**
$params = array(
    'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
    'PROP_SKU_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO'],
    'MAX_WIDTH' => 170,
    'MAX_HEIGHT' => 170,
);
**/

//RSDevFunc::GetDataForProductItem($arResult['ITEMS'], $params );

$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(
    array(
        'MAX_WIDTH'=>170,
        'MAX_HEIGHT'=>170
    )
);
