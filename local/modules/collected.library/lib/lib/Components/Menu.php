<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Components;

/**
 * Class Menu
 * @package Collected\Components
 */
class Menu extends \Collected\Base\Component
{
    protected static $componentName = 'bitrix:menu';
    protected static $params = array(
        'ALLOW_MULTI_SELECT' => 'N',
        'ROOT_MENU_TYPE' => 'top',
        'CHILD_MENU_TYPE' => 'left',
        'DELAY' => 'N',
        'MAX_LEVEL' => '3',
        'MENU_CACHE_GET_VARS' => array(),
        'MENU_CACHE_TIME' => '3600',
        'MENU_CACHE_TYPE' => 'A',
        'MENU_CACHE_USE_GROUPS' => 'N',
        'USE_EXT' => 'Y',
        'COMPONENT_TEMPLATE' => '',
    );
}