<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

foreach ($arParams as $name => $prop){
    if(preg_match('/^ICON_NOVELTY_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_NOVELTY_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^ICON_DEALS_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_DEALS_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^ICON_DISCOUNT_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_DISCOUNT_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^BRAND_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['BRAND_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^OFFER_TREE_COLOR_PROPS_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['OFFER_TREE_COLOR_PROPS'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^ICON_MEN_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_MEN_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if(preg_match('/^ICON_WOMEN_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if(0 >= $iBlockID){
            continue;
        }
        if('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_WOMEN_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
}

$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/result_modifier.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}
