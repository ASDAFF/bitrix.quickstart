<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?$APPLICATION->IncludeComponent(
	"smartrealt:catalog.list",
	"",
	Array(
		"TYPE_CODE" => $arResult["VARIABLES"]["TYPE_CODE"],
        "RUBRIC_CODE" => $arResult["VARIABLES"]["TRANSACTION_TYPE"],
        "COUNT_ON_PAGE" => $arParams["COUNT_ON_PAGE"],
        "LIST_IMAGE_WIDTH" => $arParams["LIST_IMAGE_WIDTH"],
        "LIST_IMAGE_HEIGHT" => $arParams["LIST_IMAGE_HEIGHT"],
        "SORT_BY" => $arParams["SORT_BY"],
		"SORT_ORDER" => $arParams["SORT_ORDER"],
		"DISPLAY_PANEL" => "N",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
	),
	$component
);?>
