<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.store.detail",
	"main",
	Array(
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"STORE" => $arResult["STORE"],
		"TITLE" => $arParams["TITLE"],
		"PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
		"PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MAP_TYPE" => $arParams["MAP_TYPE"],
		"GOOGLE_API_KEY" => $arParams["GOOGLE_API_KEY"],
	),
	$component
);?>
<?
if ($arParams['SET_TITLE'] == 'Y') {
	$APPLICATION->SetTitle($_SESSION['SHOP_TITLE']);
	$APPLICATION->SetPageProperty("title", $_SESSION['SHOP_TITLE']);
}
$APPLICATION->AddChainItem($_SESSION['SHOP_TITLE'], "");
?>