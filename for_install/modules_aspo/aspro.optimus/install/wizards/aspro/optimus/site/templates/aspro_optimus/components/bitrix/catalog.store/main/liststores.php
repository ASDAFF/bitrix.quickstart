<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.store.list",
	"main",
	Array(
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"PHONE" => $arParams["PHONE"],
		"SCHEDULE" => $arParams["SCHEDULE"],
		"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
		"TITLE" => $arParams["TITLE"],
		"SET_TITLE" => "N",
		"PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
		"PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
		"MAP_TYPE" => $arParams["MAP_TYPE"],
		"GOOGLE_API_KEY" => $arParams["GOOGLE_API_KEY"],
	),
	$component
);?>