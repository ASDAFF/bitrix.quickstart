<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Интернет-магазин детских товаров");
?>
<?

$APPLICATION->IncludeComponent("bitrix:news.list", "banner", array(
    "IBLOCK_TYPE" => "slider",
    "IBLOCK_ID" => "#SLIDERS_IBLOCK_ID#",
    "NEWS_COUNT" => "15",
    "SORT_BY1" => "ID",
    "SORT_ORDER1" => "DESC",
    "SORT_BY2" => "ACTIVE_FROM",
    "SORT_ORDER2" => "DESC",
    "FILTER_NAME" => "",
    "FIELD_CODE" => array(
        0 => "DETAIL_PICTURE",
        1 => "",
    ),
    "PROPERTY_CODE" => array(
        0 => "",
        1 => "",
    ),
    "CHECK_DATES" => "Y",
    "DETAIL_URL" => "",
    "AJAX_MODE" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_HISTORY" => "N",
    "CACHE_TYPE" => "N",
    "CACHE_TIME" => "36000000",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "PREVIEW_TRUNCATE_LEN" => "",
    "ACTIVE_DATE_FORMAT" => "d.m.Y",
    "SET_TITLE" => "N",
    "SET_STATUS_404" => "N",
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
    "ADD_SECTIONS_CHAIN" => "N",
    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
    "PARENT_SECTION" => "",
    "PARENT_SECTION_CODE" => "",
    "INCLUDE_SUBSECTIONS" => "Y",
    "PAGER_TEMPLATE" => ".default",
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "N",
    "PAGER_TITLE" => "Объекты",
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_DESC_NUMBERING" => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "N",
    "AJAX_OPTION_ADDITIONAL" => ""
        ), false
);
?> 
<?

$APPLICATION->IncludeComponent("lenal:eshop.catalog.top", ".default", array(
    "IBLOCK_TYPE_ID" => "catalog",
    "IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
    "ELEMENT_SORT_FIELD" => "RAND",
    "ELEMENT_SORT_ORDER" => "asc",
    "ELEMENT_COUNT" => "8",
    "FLAG_PROPERTY_CODE" => "NEWPRODUCT",
    "OFFERS_LIMIT" => "5",
    "OFFERS_FIELD_CODE" => array(
        0 => "NAME",
        1 => "",
    ),
    "OFFERS_PROPERTY_CODE" => array(
        0 => "",
        1 => "COLOR",
        2 => "WIDTH",
        3 => "",
    ),
    "OFFERS_SORT_FIELD" => "sort",
    "OFFERS_SORT_ORDER" => "asc",
    "ACTION_VARIABLE" => "action",
    "PRODUCT_ID_VARIABLE" => "id_top2",
    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
    "PRODUCT_PROPS_VARIABLE" => "prop",
    "SECTION_ID_VARIABLE" => "SECTION_ID",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "180",
    "CACHE_GROUPS" => "Y",
    "DISPLAY_COMPARE" => "N",
    "PRICE_CODE" => array(
        0 => "BASE",
    ),
    "USE_PRICE_COUNT" => "N",
    "SHOW_PRICE_COUNT" => "1",
    "PRICE_VAT_INCLUDE" => "Y",
    "CONVERT_CURRENCY" => "N",
    "OFFERS_CART_PROPERTIES" => array(
    ),
    "DISPLAY_IMG_WIDTH" => "475",
    "DISPLAY_IMG_HEIGHT" => "500",
    "SHARPEN" => "90"
        ), false
);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>