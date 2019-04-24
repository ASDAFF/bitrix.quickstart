<?php

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$menu = array(
    array(
        'parent_menu' => 'global_menu_content',
        'sort' => 400,
        'text' => Loc::getMessage('LEMA_LIB_MENU_TITLE'),
        'title' => Loc::getMessage('LEMA_LIB_MENU_TITLE'),
        'url' => 'lema_lib_index.php',
        'items_id' => 'menu_references',
        'items' => array(
            array(
                'text' => Loc::getMessage('LEMA_LIB_SUBMENU_TITLE'),
                'url' => 'lema_lib_index.php?param1=paramval&lang=' . LANGUAGE_ID,
                'more_url' => array('lema_lib_index.php?param1=paramval&lang=' . LANGUAGE_ID),
                'title' => Loc::getMessage('LEMA_LIB_SUBMENU_TITLE'),
            ),
        ),
    ),
);

return $menu;
