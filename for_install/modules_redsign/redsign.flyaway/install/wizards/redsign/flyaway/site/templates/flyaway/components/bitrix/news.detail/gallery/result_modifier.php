<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('redsign.devfunc')) {
    return;
}
$arParams['RSMONOPOLY_PROP_MORE_PHOTO'] = "MORE_PHOTO";
if(!empty($arResult)) {
	$params = array(
		'PROP_MORE_PHOTO' => $arParams['RSMONOPOLY_PROP_MORE_PHOTO'],
		'MAX_WIDTH' => 300,
		'MAX_HEIGHT' => 300,
		'PAGE' => 'detail',
	);
	$arItems = array(0 => &$arResult);
	RSDevFunc::GetDataForProductItem($arItems,$params);
}

$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
