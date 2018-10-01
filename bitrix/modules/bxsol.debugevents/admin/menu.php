<?php
/**
 * Mineev Aleksey (2016 ©)
 * alekseym@bxsolutions.ru
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    'parent_menu' => 'global_menu_settings',
    'sort' => 300,
    'url' => 'bxsol_debug_events.php',
    'text' => Loc::getMessage('BXSOL_DEBUG_EVENTS_MENU_NAME'),
    'title' => Loc::getMessage('BXSOL_DEBUG_EVENTS_MENU_TITLE'),
    'icon' => 'bxsol_events__menu_icon',
    'page_icon' => 'bxsol_events__menu_icon',
);
