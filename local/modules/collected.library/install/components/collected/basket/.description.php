<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    'NAME' => Loc::getMessage('BASKET_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('BASKET_COMPONENT_DESCRIPTION'),
    'PATH' => array(
        'ID' => 'wm_components',
        'CHILD' => array(
            'ID' => 'curdate',
            'NAME' => Loc::getMessage('BASKET_COMPONENT_NAME')
        )
    ),
    'ICON' => '/images/icon.gif',
);