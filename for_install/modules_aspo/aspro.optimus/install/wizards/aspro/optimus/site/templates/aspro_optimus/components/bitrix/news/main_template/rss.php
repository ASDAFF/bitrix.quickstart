<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?header("Content-type: text/xml");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:rss.out",
	"main",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"NUM_NEWS" => $arParams["NUM_NEWS"],
		"NUM_DAYS" => $arParams["NUM_DAYS"],
		"YANDEX" => $arParams["YANDEX"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
	),
	$component
);?>