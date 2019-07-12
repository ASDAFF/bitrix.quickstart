<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("#COMPANY_NAME#");
?>

<div class="slide">
    <div class="inner">
        <div class="title"><span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/services_1.php", "EDIT_TEMPLATE" => "" ), false );?></span></div>
        <p class="margin1"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/services_2.php", "EDIT_TEMPLATE" => "" ), false );?></p>
        <?$GLOBALS["arrFilter"] = array("PROPERTY_DISPLAY_ON_MAIN_VALUE" => "Y");?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "services_2",
            array(
                "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                "IBLOCK_ID" => "#INNET_IBLOCK_ID_SERVICES#",
                "NEWS_COUNT" => "6",
                "SORT_BY1" => "SORT",
                "SORT_ORDER1" => "ASC",
                "SORT_BY2" => "",
                "SORT_ORDER2" => "",
                "FILTER_NAME" => "arrFilter",
                "FIELD_CODE" => array(
                    0 => "ID",
                ),
                "PROPERTY_CODE" => array(),
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "Y",
                "PREVIEW_TRUNCATE_LEN" => "",
                "ACTIVE_DATE_FORMAT" => "j F Y",
                "SET_STATUS_404" => "N",
                "SET_TITLE" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                "PARENT_SECTION" => "",
                "PARENT_SECTION_CODE" => "",
                "INCLUDE_SUBSECTIONS" => "N",
                "PAGER_TEMPLATE" => "",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "SET_BROWSER_TITLE" => "N",
                "SET_META_KEYWORDS" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_LAST_MODIFIED" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => ""
            ),
            false
        );?>
        <div class="cons in-row-mid">
            <div class="col1">
                <div class="title4"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/services_3.php", "EDIT_TEMPLATE" => "" ), false );?></div>
                <p><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/services_4.php", "EDIT_TEMPLATE" => "" ), false );?></p>
            </div>
            <div class="col2">
                <a class="btn popbutton" data-window="7"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/services_5.php", "EDIT_TEMPLATE" => "" ), false );?></a>
            </div>
        </div>
    </div>
</div>

<div class="slide pt0">
    <div class="inner">
        <div class="title"><span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/catalog_1.php", "EDIT_TEMPLATE" => "" ), false );?></span></div>
        <p class="margin1"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/catalog_2.php", "EDIT_TEMPLATE" => "" ), false );?></p>
        <?$APPLICATION->IncludeComponent("innet:topics.goods", "innet", array(
                "IBLOCK_TYPE" => "innet_catalog_" . SITE_ID,
                "IBLOCK_ID" => "#INNET_IBLOCK_ID_CATALOG#",
                "COUNT_SECTION" => "4",
                "COUNT_SECTION_NESTED" => "",
                "UF_CODE" => "UF_SECTION_LIST",
                "SORT_SECTION" => "ASC",
                "SORT_SUB_SECTION" => "ASC",
                "ELEMENT_IN_SECTION" => "Y",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "WIDTH" => "190",
                "HEIGHT" => "150"
            ),
            false
        );?>
    </div>
</div>

<div class="slide pt1">
    <div class="inner">
        <div class="title"><span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/catalog_3.php", "EDIT_TEMPLATE" => "" ), false );?></span></div>
        <?$GLOBALS["arrFilter2"] = array("PROPERTY_DISPLAY_ON_MAIN_VALUE" => "Y");?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "innet",
            array(
                "IBLOCK_TYPE" => "innet_catalog_" . SITE_ID,
                "IBLOCK_ID" => "#INNET_IBLOCK_ID_CATALOG#",

                "INNET_IBLOCK_ID_ORDER" => "#INNET_IBLOCK_ID_CATALOG_ORDERS#",
                "INNET_IBLOCK_ID_QUESTIONS" => "#INNET_IBLOCK_ID_CATALOG_QUESTIONS#",
                "INNET_IBLOCK_ID_REVIEWS" => "#INNET_IBLOCK_ID_CATALOG_REVIEWS#",
                "INNET_ALLOW_REVIEWS" => "Y",
                "INNET_ELEMENT_USE_SORT" => "Y",
                "INNET_PREVIEW_TEXT_DETAIL" => "Y",
                "INNET_DISPLAY_PROPERTIES_SECTION" => "Y",
                "INNET_PREVIEW_TEXT_SECTION" => "N",
                "INNET_PREVIEW_TEXT_SECTION_COUNT" => "300",
                "INNET_USE_DELAY" => "Y",
                "INNET_DELAY_PATH" => "#SITE_DIR#ajax/addDelay.php",
                "INNET_MESS_BTN_DELAY" => "Отложить товар",
                "DISPLAY_COMPARE" => "Y",

                "BASKET_URL" => "#SITE_DIR#personal/cart/",
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "SECTION_USER_FIELDS" => array(),
                "ELEMENT_SORT_FIELD" => "sort",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_SORT_FIELD2" => "id",
                "ELEMENT_SORT_ORDER2" => "desc",
                "FILTER_NAME" => "arrFilter2",
                "INCLUDE_SUBSECTIONS" => "Y",
                "SHOW_ALL_WO_SECTION" => "Y",
                "HIDE_NOT_AVAILABLE" => "N",
                "PAGE_ELEMENT_COUNT" => "10",
                "LINE_ELEMENT_COUNT" => "",
                "PROPERTY_CODE" => array(
                    0 => "ARTICLE",
                    1 => "BRAND",
                ),
                "OFFERS_FIELD_CODE" => array(),
                "OFFERS_PROPERTY_CODE" => array(
                    0 => "COLOR_REF",
                    1 => "SIZE_MONITOR",
                    2 => "HEIGHT",
                    3 => "STOCK_PRINT",
                    4 => "MORE_PHOTO",
                ),
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_FIELD2" => "id",
                "OFFERS_SORT_ORDER2" => "desc",
                "OFFERS_LIMIT" => "0",
                "TEMPLATE_THEME" => "",
                "PRODUCT_DISPLAY_MODE" => "Y",
                "ADD_PICT_PROP" => "MORE_PHOTO",
                "LABEL_PROP" => "-",
                "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
                "OFFER_TREE_PROPS" => array(
                    0 => "COLOR_REF",
                    1 => "SIZE_MONITOR",
                ),
                "PRODUCT_SUBSCRIPTION" => "Y",
                "SHOW_DISCOUNT_PERCENT" => "Y",
                "SHOW_OLD_PRICE" => "Y",
                "SHOW_CLOSE_POPUP" => "Y",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_NOT_AVAILABLE" => "Нет в наличии",
                "SECTION_URL" => "",
                "DETAIL_URL" => "",
                "SECTION_ID_VARIABLE" => "",
                "SEF_MODE" => "N",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "SET_TITLE" => "N",
                "SET_BROWSER_TITLE" => "N",
                "BROWSER_TITLE" => "-",
                "SET_META_KEYWORDS" => "N",
                "META_KEYWORDS" => "-",
                "SET_META_DESCRIPTION" => "N",
                "META_DESCRIPTION" => "-",
                "SET_LAST_MODIFIED" => "N",
                "USE_MAIN_ELEMENT_SECTION" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "CACHE_FILTER" => "N",
                "ACTION_VARIABLE" => "action",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRICE_CODE" => array(
                    0 => "BASE",
                ),
                "USE_PRICE_COUNT" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "PRICE_VAT_INCLUDE" => "N",
                "CONVERT_CURRENCY" => "Y",
                "USE_PRODUCT_QUANTITY" => "Y",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PARTIAL_PRODUCT_PROPERTIES" => "Y",
                "PRODUCT_PROPERTIES" => array(),
                "OFFERS_CART_PROPERTIES" => array(
                    0 => "COLOR_REF",
                    1 => "SIZE_MONITOR",
                ),
                "ADD_TO_BASKET_ACTION" => "ADD",
                "PAGER_TEMPLATE" => "",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Товары",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
                "BACKGROUND_IMAGE" => "-",
                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                "CURRENCY_ID" => "RUB"
            ),
            false
        );?>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
