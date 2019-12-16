<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

$arParams['SLIDER_AUTOPLAY'] = (isset($arParams['SLIDER_AUTOPLAY']) && $arParams['SLIDER_AUTOPLAY'] === 'N' ? false : true);
$arParams['SLIDER_LAZYLOAD'] = (isset($arParams['SLIDER_LAZYLOAD']) && $arParams['SLIDER_LAZYLOAD'] === 'N' ? false : true);
$arParams['SLIDER_LOOP'] = (isset($arParams['SLIDER_LOOP']) && $arParams['SLIDER_LOOP'] === 'N' ? false : true);
$arParams['SLIDER_CENTER'] = (isset($arParams['SLIDER_CENTER']) && $arParams['SLIDER_CENTER'] === 'N' ? false : true);

if ($arParams['SLIDER_AUTOPLAY']) {
    $arParams['SLIDER_AUTOPLAY_SPEED'] = intval($arParams['SLIDER_AUTOPLAY_SPEED']);
    if ($arParams['SLIDER_AUTOPLAY_SPEED'] < 0) {
        $arParams['SLIDER_AUTOPLAY_SPEED'] = 2000;
    }
    $arParams['SLIDER_AUTOPLAY_TIMEOUT'] = intval($arParams['SLIDER_AUTOPLAY_TIMEOUT']);
    if ($arParams['SLIDER_AUTOPLAY_TIMEOUT'] < 0) {
        $arParams['SLIDER_AUTOPLAY_TIMEOUT'] = 5000;
    }
}
$arParams['SLIDER_ITEMS'] = intval($arParams['SLIDER_ITEMS']);
if ($arParams['SLIDER_ITEMS'] <= 0) {
    $arParams['SLIDER_ITEMS'] = 1;
}
$arParams['SLIDER_ANIMATEIN'] =  (isset($arParams['SLIDER_ANIMATEIN']) && strlen($arParams['SLIDER_ANIMATEIN']) > 0 ? $arParams['SLIDER_ANIMATEIN'] : false);
$arParams['SLIDER_ANIMATEOUT'] =  (isset($arParams['SLIDER_ANIMATEOUT']) && strlen($arParams['SLIDER_ANIMATEOUT']) > 0 ? $arParams['SLIDER_ANIMATEOUT'] : false);