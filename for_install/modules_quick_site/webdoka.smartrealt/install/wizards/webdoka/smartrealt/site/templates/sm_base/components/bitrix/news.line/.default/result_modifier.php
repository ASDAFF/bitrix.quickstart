<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       # 
* # mailto:info@smartrealt.com      #
* ###################################
*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$rsIBlock = CIBlock::GetByID($arResult['IBLOCK_ID']);

if ($arIBlock = $rsIBlock->Fetch())
{
    $arResult['LIST_PAGE_URL'] = $arIBlock['LIST_PAGE_URL'];
    $arResult['LIST_PAGE_URL'] = str_replace('#SITE_DIR#', SITE_DIR, $arIBlock['LIST_PAGE_URL']);
}


?>