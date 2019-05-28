<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use \Bitrix\Main\EventManager;

$oEventManager = EventManager::getInstance();
$oEventManager->addEventHandler('main', 'OnBuildGlobalMenu', function (&$arGlobalMenu, &$arModuleMenu)
{

    $sModule = str_replace('.', '_', basename(dirname(dirname(__FILE__))));

    $arMenu = array();
    $sKeyMenu = '';

    $sGlobalMenu = 'global_menu_services';

    foreach ($arModuleMenu as $sKey => $arVal) {
        if ($arVal['parent_menu'] === $sGlobalMenu && $arVal['section'] === $sModule) {
            $sKeyMenu = $sKey;
            $arMenu = $arVal;
            break;
        }
    }

    if (empty($arMenu)) {
        $arMenu = array(
            'parent_menu' => $sGlobalMenu,
            'section' => $sModule,
            'sort' => 0,
            'text' => 'Дисконтные карты',
            'title' => 'Дисконтные карты',
            'icon' => 'sale_menu_icon_catalog',
            'page_icon' => 'sale_menu_icon_catalog',
            'items_id' => 'menu_'.$sModule,
            'items' => array(),
        );
    }

    $arMenu['items'][] = array(
        'text' => 'Обновление данных',
        'title' => 'Обновление данных',
        'url' => "discount_cards_updater.php",
        'more_url' => array(
            "discount_cards_updater.php"
        ),
    );

    if ($sKeyMenu) {
        $arModuleMenu[$sKeyMenu] = $arMenu;
    } else {
        $arModuleMenu[] = $arMenu;
    }
});