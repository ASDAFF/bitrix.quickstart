<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    'NAME' => Loc::getMessage('RS_COMPONENT_LOCATION_MAIN_NAME'),
    'DESCRIPTION' => Loc::getMessage('RS_COMPONENT_LOCATION_MAIN_DESC'),
    'ICON' => '',
    'CACHE_PATH' => 'Y',
    'PATH' => array(
        'ID' => 'alfa_com',
        'SORT' => 5000,
        'NAME' => Loc::getMessage('RS_COMPONENT_LOCATION_PATH_MAIN_NAME'),
    ),
);
