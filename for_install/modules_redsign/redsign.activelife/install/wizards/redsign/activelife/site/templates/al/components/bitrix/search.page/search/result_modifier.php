<?php

use \Bitrix\Main\Loader;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (Loader::includeModule('redsign.devfunc')) {
	$arResult = RSDevFuncResultModifier::SearchPage($arResult);
}

