<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$menu = array(
    array(
        'parent_menu' => 'global_menu_content',
        'sort' => 400,
        'text' => Loc::getMessage('COLLECT_LIB_MENU_TITLE'),
        'title' => Loc::getMessage('COLLECT_LIB_MENU_TITLE'),
        'url' => 'collected_library_index.php',
        'items_id' => 'menu_references',
        'items' => array(
            array(
                'text' => Loc::getMessage('COLLECT_LIB_SUBMENU_TITLE'),
                'url' => 'collected_library_index.php?param1=paramval&lang=' . LANGUAGE_ID,
                'more_url' => array('collected_library_index.php?param1=paramval&lang=' . LANGUAGE_ID),
                'title' => Loc::getMessage('COLLECT_LIB_SUBMENU_TITLE'),
            ),
        ),
    ),
);

return $menu;
