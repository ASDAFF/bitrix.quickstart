<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/result_modifier.php');

if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 && is_array($arParams['PRICE_CODE']) && count($arParams['PRICE_CODE'])>0 ) {
	$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
}