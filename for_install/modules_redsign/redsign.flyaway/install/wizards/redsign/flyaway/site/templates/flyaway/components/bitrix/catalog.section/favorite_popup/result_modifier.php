<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('redsign.devfunc')) {
    return;
}

$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array(
    'MAX_WIDTH'=>300,
    'MAX_HEIGHT'=>300
));

