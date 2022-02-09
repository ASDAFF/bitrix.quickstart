<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// place your component here
$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"",
	Array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"LIST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]['list'],
	),
	$component
);

?>