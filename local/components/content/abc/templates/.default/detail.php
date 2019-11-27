<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->SetTitle($arResult['PROPERTY_VALUE']);
$APPLICATION->AddChainItem($arResult['PROPERTY_VALUE']);
$APPLICATION->IncludeComponent("bitrix:news.list", "", array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"FILTER_NAME" => $arParams['FILTER_NAME'],
    "INCLUDE_SUBSECTIONS" => "Y",
    //"SHOW_ALL_WO_SECTION" => "Y",
    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
    "CACHE_TIME" => $arParams['CACHE_TIME'],
	),
	$component
);?>