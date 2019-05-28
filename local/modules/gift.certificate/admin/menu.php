<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use \Bitrix\Main\EventManager;

$oEventManager = EventManager::getInstance();
$oEventManager->addEventHandler('main', 'OnBuildGlobalMenu', function (&$arGlobalMenu, &$arModuleMenu)
{

    $arMenu = array();
    $sKeyMenu = '';

    $sGlobalMenu = 'global_menu_marketing';

    foreach ($arModuleMenu as $sKey => $arVal) {
        if ($arVal['parent_menu'] === $sGlobalMenu && $arVal['items_id'] === 'menu_sale_discounts') {
            $sKeyMenu = $sKey;
            $arMenu = $arVal;
            break;
        }
    }

    $arMenu['items'][0]['items'][] = array(
        'text' => 'Подарочные сертификаты',
        'title' => 'Подарочные сертификаты',
        'url' => "gift_certificate.php",
        'more_url' => array(
            "gift_certificate.php",
            "gift_certificate_edit.php"
        ),
    );

    if ($sKeyMenu) {
        $arModuleMenu[$sKeyMenu] = $arMenu;
    } else {
        $arModuleMenu[] = $arMenu;
    }
});