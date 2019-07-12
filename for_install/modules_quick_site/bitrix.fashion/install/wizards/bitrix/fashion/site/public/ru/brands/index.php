<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<? $APPLICATION->SetTitle("Бренды"); ?>


<? $APPLICATION->IncludeComponent(
    "fashion:catalog",
    "brands_fashion",
    array(
        "BRANDS_IBLOCK_ID" => "#CATALOG_BRANDS_IBLOCK_ID#",
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
        "BASKET_URL" => "/personal/basket.php",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "#SITE_DIR#brands/",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "SET_TITLE" => "Y",
        "SET_STATUS_404" => "Y",
        "USE_FILTER" => "Y",
        "FILTER_NAME" => "arrFilter",
        "FILTER_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_PRICE_CODE" => array(
        ),
        "FILTER_OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_OFFERS_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "USE_REVIEW" => "N",
        "USE_COMPARE" => "N",
        "PRICE_CODE" => array(
            0 => "BASE",
        ),
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "OFFERS_CART_PROPERTIES" => array(
            0 => "item_color",
            1 => "item_size",
        ),
        "SHOW_TOP_ELEMENTS" => "N",
        "PAGE_ELEMENT_COUNT" => "12",
        "LINE_ELEMENT_COUNT" => "3",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_ORDER" => "asc",
        "LIST_PROPERTY_CODE" => array(
            0 => "models_hit",
            1 => "models_new",
            2 => "models_rating",
            3 => "",
        ),
        "INCLUDE_SUBSECTIONS" => "Y",
        "LIST_META_KEYWORDS" => "UF_KEYWORDS",
        "LIST_META_DESCRIPTION" => "UF_DESCRIPTION",
        "LIST_BROWSER_TITLE" => "UF_TITLE",
        "LIST_SHOW_INSTOCK" => "Y",
        "LIST_OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "LIST_OFFERS_PROPERTY_CODE" => array(
            0 => "item_color",
            1 => "item_size",
            2 => "",
        ),
        "LIST_OFFERS_LIMIT" => "0",
        "DETAIL_PROPERTY_CODE" => array(
            0 => "similar_products",
            1 => "models_hit",
            2 => "models_article",
            3 => "",
        ),
        "DETAIL_META_KEYWORDS" => "-",
        "DETAIL_META_DESCRIPTION" => "-",
        "DETAIL_BROWSER_TITLE" => "-",
        "DETAIL_OFFERS_FIELD_CODE" => array(
            0 => "WEIGHT",
            1 => "",
        ),
        "DETAIL_OFFERS_PROPERTY_CODE" => array(
            0 => "item_color",
            1 => "item_size",
            2 => "item_more_photo",
            3 => "",
        ),
        "LINK_IBLOCK_TYPE" => "catalog",
        "LINK_IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
        "LINK_PROPERTY_SID" => "similar_products",
        "LINK_ELEMENTS_URL" => "",
        "USE_ALSO_BUY" => "N",
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "asc",
        "DISPLAY_TOP_PAGER" => "Y",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "Y",
        "PAGER_TEMPLATE" => "catalog",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "brand"   => "#BRAND_CODE#/",
            "section" => "#BRAND_CODE#/#SECTION_CODE#/",
            "element" => "#BRAND_CODE#/#SECTION_CODE#/#ELEMENT_CODE#/",
            "compare" => "compare.php?action=#ACTION_CODE#",
        ),
        "VARIABLE_ALIASES" => array(
            "compare" => array(
                "ACTION_CODE" => "action",
            ),
        )
    ),
    false
); ?>


<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>