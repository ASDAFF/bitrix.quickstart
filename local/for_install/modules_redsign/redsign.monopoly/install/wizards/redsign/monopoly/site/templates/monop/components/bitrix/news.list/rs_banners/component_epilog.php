<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Page\Asset;

$assets = array(
    "owl" => array(
        "scripts" => array(
            SITE_TEMPLATE_PATH.'/components/bitrix/news.list/rs_banners/js/rs_banners.js'
        ),
        "styles" => array(
            SITE_TEMPLATE_PATH.'/components/bitrix/news.list/rs_banners/styles/redsign_banners.css',
            SITE_TEMPLATE_PATH.'/components/bitrix/news.list/rs_banners/styles/theme.css',
        )
    ),
);

$adapter = "owl";
if(
    !empty($assets[$arResult['SLIDER_ADAPTER']]) &&
    is_array($assets[$arResult['SLIDER_ADAPTER']])
) {
    $adapter = $arResult['SLIDER_ADAPTER'];
}

if(!empty($assets[$adapter]['scripts']) && is_array($assets[$adapter]['scripts'])) {
    foreach($assets[$adapter]['scripts'] as $script) {
        Asset::getInstance()->addJs($script);
    }
}

if(!empty($assets[$adapter]['styles']) && is_array($assets[$adapter]['styles'])) {
    foreach($assets[$adapter]['styles'] as $style) {
        Asset::getInstance()->addCss($style);
    }
}