<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('redsign.devfunc')) {
    return;
}

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH' => 220, 'MAX_HEIGHT' => 220));
$arResult['NO_PHOTO'] = $arResult['NO_PHOTO']['src'];
// /get no photo