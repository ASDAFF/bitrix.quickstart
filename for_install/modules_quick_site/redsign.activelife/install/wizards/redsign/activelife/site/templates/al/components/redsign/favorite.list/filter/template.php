<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$frame = $this->createFrame()->begin('');
    $arIDs = array();
    if (is_array($arResult['ITEMS']) && 0 < count($arResult['ITEMS'])) {
        foreach($arResult['ITEMS'] as $arItem) {
            $arIDs[$arItem['ELEMENT_ID']] = true;
        }
     }
     ?><script>appSLine.favoriteList = <?=json_encode($arIDs)?>;</script><?
$frame->end();