<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// place your component here
$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"",
	Array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]['detail'],
	),
	$component
);

?>