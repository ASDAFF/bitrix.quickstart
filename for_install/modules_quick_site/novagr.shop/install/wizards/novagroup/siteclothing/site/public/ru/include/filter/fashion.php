<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent("novagr.shop:fashion.filter", "default", array(
        "CATALOG_IBLOCK_TYPE" => "catalog",
        "CATALOG_IBLOCK_CODE" => "novagr_standard_images",
        "ROOT_PATH" => SITE_DIR."catalog/",
        "TRADEOF_IBLOCK_TYPE" => "-",
        "TRADEOF_IBLOCK_CODE" => "",
        "SPECIAL_SORT_ORDER" => "-5",
        "SECTION_SORT_ORDER" => "-20",
        "PRICE_SORT_ORDER" => "-10",
        "SHOW_PRICE_SLIDER" => "N",
        "SHOW_SECTION" => "N",
        "BRAND_ROOT" => SITE_DIR."brand/",
        "FASHION_MODE" => "Y",
        "FASHION_ROOT" => SITE_DIR."imageries/",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600"
    ),
    false
);?>