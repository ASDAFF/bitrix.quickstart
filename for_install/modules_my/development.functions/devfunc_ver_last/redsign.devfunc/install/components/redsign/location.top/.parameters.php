<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'COUNT_ITEMS' => array(
            'NAME' => Loc::getMessage('RS_LOCATION_TOP_PARAMETERS_COUNT_ITEMS'),
            "PARENT" => "BASE",
            'TYPE' => 'STRING',
            'DEFAULT' => 15
        )
    ),
);
