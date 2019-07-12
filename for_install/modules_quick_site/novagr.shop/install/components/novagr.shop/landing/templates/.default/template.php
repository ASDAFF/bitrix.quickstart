<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$elmid = $APPLICATION->IncludeComponent(
    "novagr.shop:catalog.element",
    "landing",
    Array(
        "LANDING_PREVIEW_TEXT" => $arResult["LANDING_ELEMENT"]["PREVIEW_TEXT"],
        "LANDING_DETAIL_TEXT" => $arResult["LANDING_ELEMENT"]["DETAIL_TEXT"],
        "ELEMENT_ID" => $arResult["LANDING_ELEMENT"]["PROPERTY_PRODUCT_ID_VALUE"],
        "LANDING_PAGE" => "Y",
        "SORT_FIELD" => "ID",
        "SORT_BY" => "DESC",
        "CATALOG_IBLOCK_TYPE" => "catalog",
        "CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
        "CATALOG_OFFERS_IBLOCK_ID" => $arParams["CATALOG_OFFERS_IBLOCK_ID"],
        "ARTICLES_IBLOCK_ID" => $arParams["ARTICLES_IBLOCK_ID"],
        "SAMPLES_IBLOCK_CODE" => "samples",
        "BRANDNAME_IBLOCK_CODE" => "vendor",
        "COLORS_IBLOCK_CODE" => "colors",
        "MATERIALS_IBLOCK_CODE" => "materials",
        "STD_SIZES_IBLOCK_CODE" => "std_sizes",
        "CATALOG_SUBSCRIBE_ENABLE" => "Y",
        "OPT_GROUP_ID" => $arParams["OPT_GROUP_ID"],
        "OPT_PRICE_ID" => $arParams["OPT_PRICE_ID"],
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "360000",
        "cs"=>$_REQUEST['cs'],
    ),
    false,
    Array(
        'ACTIVE_COMPONENT' => 'Y',
    )
);?>

