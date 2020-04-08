<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arResult["INCLUDE_PERSONAL"] = $arParams['SHOW_PERSONAL'];
$arResult["INCLUDE_WORK"] = $arParams['SHOW_WORK'];
if (!$arParams['SHOW_TZ'] != 'Y') {
	$arResult["TIME_ZONE_ENABLED"] = false;
}
if (!$arParams['SHOW_FORUM'] != 'Y') {
	$arResult["INCLUDE_FORUM"] = "N";
}
if (!$arParams['SHOW_BLOG'] != 'Y') {
	$arResult["INCLUDE_BLOG"] = "N";
}
if (!$arParams['SHOW_LEARNING'] != 'Y') {
	$arResult["INCLUDE_LEARNING"] = "N";
}
if (!$arParams['SHOW_ADMIN'] != 'Y') {
	$arResult["IS_ADMIN"] = false;
}