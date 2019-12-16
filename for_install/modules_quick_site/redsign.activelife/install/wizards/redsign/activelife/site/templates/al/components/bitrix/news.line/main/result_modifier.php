<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

foreach($arResult['ITEMS'] as $key => $arItem)  {
    $arResult['ITEMS'][$key]['IBLOCK_LINK'] = str_replace('//', '/', str_replace($arItem['CODE'], '', $arItem['DETAIL_PAGE_URL']));
}