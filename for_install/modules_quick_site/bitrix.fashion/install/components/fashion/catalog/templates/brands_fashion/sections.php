<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent("fashion:brands.list", "", array(
    "MODELS_IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "BRANDS_IBLOCK_ID" => $arParams["BRANDS_IBLOCK_ID"],
    "PROPERTY_BRAND" => "fil_models_brand",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "36000000",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "N",
    "PAGER_TITLE" => "title",
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_TEMPLATE" => "catalog",
    "PAGER_DESC_NUMBERING" => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "N",
    "PAGE_ELEMENT_COUNT" => "4",
	),
	$component
);
?>