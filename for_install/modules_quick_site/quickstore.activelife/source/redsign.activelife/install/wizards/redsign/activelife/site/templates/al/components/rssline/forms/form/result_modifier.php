<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) {
    die();
}

if(empty($arParams['REQUIRED_FIELDS']) || !is_array($arParams['REQUIRED_FIELDS'])) {
    $arParams['REQUIRED_FIELDS'] = array();
}
if(empty($arParams['DISABLED_FIELDS']) || !is_array($arParams['DISABLED_FIELDS'])) {
    $arParams['DISABLED_FIELDS'] = array();
}
