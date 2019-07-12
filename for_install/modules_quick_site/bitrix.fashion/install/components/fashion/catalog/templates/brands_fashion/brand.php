<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="brand-detail">
<?
$APPLICATION->IncludeComponent(
    "fashion:brands.info",
    "",
    Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"BRANDS_IBLOCK_ID" => $arParams["BRANDS_IBLOCK_ID"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
		
		"BRAND_CODE" => $arResult["VARIABLES"]["BRAND_CODE"]
    ),
    $component
);


$arAvailableSort = array(
    "name" => Array("name", "asc"),
    "brand" => Array("PROPERTY_fil_models_brand", "asc"),
    "popularity" => Array("PROPERTY_models_rating", "desc"),

);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "popularity";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];

if($sort=="popularity"){
    $sort = 'PROPERTY_models_rating';
}

$arPerPage = array(12, 24, 36);
$elementCount = intval($_REQUEST["count"]) > 0 ? intval($_REQUEST["count"]) : $arPerPage[0];

$arParams["LIST_SHOW_INSTOCK"] = $arParams["LIST_SHOW_INSTOCK"]==="Y";
if($arParams["LIST_SHOW_INSTOCK"]=='Y'){
    global $arrFilter;
    if(isset($_REQUEST['instock'])&&$_REQUEST['instock']=='Y')
        $arrFilter['OFFERS']['>CATALOG_QUANTITY'] = 0;
}

$APPLICATION->IncludeComponent(
    "fashion:catalog.section",
    "",
    Array(
        "BRAND_VARIABLE" => $arResult["VARIABLES"]["BRAND_CODE"],
        "BY_LINK" => "Y",
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ELEMENT_SORT_FIELD" => $sort,
        "ELEMENT_SORT_ORDER" => $sort_order,
        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
        "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
        "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
        "PAGE_ELEMENT_COUNT" => $elementCount,
        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
        "ADD_SECTIONS_CHAIN" => "Y",
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],

        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
        "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
        "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
        "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],

        "SHOW_INSTOCK" => $arParams["LIST_SHOW_INSTOCK"],
		"SET_TITLE" => "N",
		"SHOW_BLOCK_NAME" => "Y"
    ),
    $component
);
?>
</div>
