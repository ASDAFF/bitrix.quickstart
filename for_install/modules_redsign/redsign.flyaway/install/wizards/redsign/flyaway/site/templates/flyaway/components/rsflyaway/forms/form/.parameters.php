<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arFields = $arCurrentValues['SHOW_FIELDS'];


$arTemplateParameters = array(
    "RS_FLYAWAY_DISABLED_FIELDS" => array()
);
