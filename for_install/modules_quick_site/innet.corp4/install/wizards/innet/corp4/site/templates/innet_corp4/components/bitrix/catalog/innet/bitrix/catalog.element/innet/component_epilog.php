<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

        <?if (!$arParams['INNET_QUICK_VIEW'] && $arParams['INNET_ALLOW_REVIEWS'] == 'Y'){?>
            <div class="tab" id="tabs-8">
                <?$GLOBALS["arrFilter"] = array("PROPERTY_ID_ELEMENT" => $arResult['ID']);?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:news.list",
                    "reviews",
                    array(
                        "IBLOCK_TYPE" => "Forms",
                        "IBLOCK_ID" => $arParams["INNET_IBLOCK_ID_REVIEWS"],
                        "NEWS_COUNT" => "20",
                        "SORT_BY1" => "ACTIVE_FROM",
                        "SORT_ORDER1" => "DESC",
                        "SORT_BY2" => "SORT",
                        "SORT_ORDER2" => "ASC",
                        "FILTER_NAME" => "arrFilter",
                        "FIELD_CODE" => array(
                            0 => "ID",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "",
                            1 => "NAME_CLIENT",
                            2 => "COMMENT",
                        ),
                        "CHECK_DATES" => "Y",
                        "DETAIL_URL" => "",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "3600",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "N",
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
                        "PAGER_TEMPLATE" => "innet",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "PAGER_TITLE" => "",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "COMPONENT_TEMPLATE" => "reviews",
                        "SET_BROWSER_TITLE" => "Y",
                        "SET_META_KEYWORDS" => "Y",
                        "SET_META_DESCRIPTION" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                    ),
                    false
                );?>

                <?$APPLICATION->IncludeComponent("innet:form", "reviews", array(
                        "USE_CAPTCHA" => "Y",
                        "EVENT_MESSAGE_ID" => array(),
                        "REQUIRED_FIELDS" => array("NAME"),
                        "AJAX_MODE" => "Y",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "EVENT_MESSAGE_TYPE" => "INNET_CATALOG_REVIEWS",
                        "EVENT_MESSAGE_TYPE_USER" => "INNET_CATALOG_REVIEWS_USER",
                        "INNET_ID_ELEMENT" => $arResult["ID"],
                        "INNET_NAME_ELEMENT" => $arResult["NAME"],
                        "INNET_IBLOCK_ID_RECORD" => $arParams['INNET_IBLOCK_ID_REVIEWS'],
                    ),
                    false
                );?>
            </div>
        <?}?>
    </div><!--#tabs-->
</div><!--.element-->


<?if (!empty($arResult['PROPERTIES']['RECOMMEND']['VALUE'])){?>
    <div class="slide pt1" style="margin: 0 0 30px 0;">
        <div class="inner">
            <div class="title" style="font-size: 30px;margin-top: 30px;"><span><?=$arResult['PROPERTIES']['RECOMMEND']['NAME']?></span></div>
            <?$GLOBALS["arrFilter"] = array("ID" => $arResult['PROPERTIES']['RECOMMEND']['VALUE']);?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "innet",
                array(
                    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_USER_FIELDS" => array(),
                    "ELEMENT_SORT_FIELD" => "sort",
                    "ELEMENT_SORT_ORDER" => "asc",
                    "ELEMENT_SORT_FIELD2" => "",
                    "ELEMENT_SORT_ORDER2" => "",
                    "FILTER_NAME" => "arrFilter",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "PAGE_ELEMENT_COUNT" => "10",
                    "LINE_ELEMENT_COUNT" => "",
                    "PROPERTY_CODE" => array(
                        0 => "ARTICLE",
                        1 => "BRAND",
                    ),
                    "OFFERS_LIMIT" => "",
                    "SECTION_URL" => "",
                    "DETAIL_URL" => "",
                    "SECTION_ID_VARIABLE" => "",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_GROUPS" => "Y",
                    "SET_META_KEYWORDS" => "N",
                    "META_KEYWORDS" => "",
                    "SET_META_DESCRIPTION" => "N",
                    "META_DESCRIPTION" => "",
                    "BROWSER_TITLE" => "-",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "DISPLAY_COMPARE" => "N",
                    "SET_TITLE" => "N",
                    "SET_STATUS_404" => "N",
                    "CACHE_FILTER" => "N",
                    "PRICE_CODE" => array(),
                    "USE_PRICE_COUNT" => "N",
                    "SHOW_PRICE_COUNT" => "",
                    "PRICE_VAT_INCLUDE" => "N",
                    "BASKET_URL" => "",
                    "ACTION_VARIABLE" => "",
                    "PRODUCT_ID_VARIABLE" => "",
                    "USE_PRODUCT_QUANTITY" => "N",
                    "ADD_PROPERTIES_TO_BASKET" => "N",
                    "PAGER_TEMPLATE" => "",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "PRODUCT_PROPERTIES" => array(),
                    "BACKGROUND_IMAGE" => "-",
                    "TEMPLATE_THEME" => "blue",
                    "ADD_PICT_PROP" => "-",
                    "LABEL_PROP" => "-",
                    "MESS_BTN_BUY" => "",
                    "MESS_BTN_ADD_TO_BASKET" => "",
                    "MESS_BTN_SUBSCRIBE" => "",
                    "MESS_BTN_COMPARE" => "",
                    "MESS_BTN_DETAIL" => "",
                    "MESS_NOT_AVAILABLE" => "",
                    "SEF_MODE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "USE_MAIN_ELEMENT_SECTION" => "N",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "SHOW_404" => "N",
                    "MESSAGE_404" => "",
                    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                    "INNET_USE_PREVIEW_TEXT_IN_SECTION" => "Y",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "OFFERS_FIELD_CODE" => array(),
                    "OFFERS_PROPERTY_CODE" => array(),
                    "OFFERS_SORT_FIELD" => "sort",
                    "OFFERS_SORT_ORDER" => "asc",
                    "OFFERS_SORT_FIELD2" => "id",
                    "OFFERS_SORT_ORDER2" => "desc",
                    "CONVERT_CURRENCY" => "N",
                    "OFFERS_CART_PROPERTIES" => array(
                        0 => "undefined",
                    )
                ),
                false
            );?>
        </div>
    </div>
<?}?>


<div id="order_product" class="popwindow" data-title="" data-height="auto">
    <div class="popup-wrap1">
        <a class="bw_close"></a>
        <?$APPLICATION->IncludeComponent("innet:form", "product_order", array(
                "USE_CAPTCHA" => "Y",
                "EVENT_MESSAGE_ID" => array(),
                "OK_TEXT" => "",
                "EMAIL_TO" => "",
                "REQUIRED_FIELDS" => array("NAME"),
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "EVENT_MESSAGE_TYPE" => "INNET_CATALOG_ORDER",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_CATALOG_ORDER_USER",
                "INNET_ID_ELEMENT" => $arResult["ID"],
                "INNET_NAME_ELEMENT" => $arResult["NAME"],
                "INNET_IBLOCK_ID_RECORD" => $arParams['INNET_IBLOCK_ID_ORDER'],
                "INNET_PRICE_ELEMENT" => $arResult['PROPERTIES']['PRICE']['VALUE'],
            ),
            false
        );?>
    </div>
</div>


<div id="question_product" class="popwindow" data-title="" data-height="auto">
    <div class="popup-wrap1">
        <a class="bw_close"></a>
        <?$APPLICATION->IncludeComponent("innet:form", "product_question", array(
                "USE_CAPTCHA" => "Y",
                "EVENT_MESSAGE_ID" => array(),
                "OK_TEXT" => "",
                "EMAIL_TO" => "",
                "REQUIRED_FIELDS" => array("NAME"),
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "EVENT_MESSAGE_TYPE" => "INNET_CATALOG_QUESTIONS",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_CATALOG_QUESTIONS_USER",
                "INNET_ID_ELEMENT" => $arResult["ID"],
                "INNET_NAME_ELEMENT" => $arResult["NAME"],
                "INNET_IBLOCK_ID_RECORD" => $arParams['INNET_IBLOCK_ID_QUESTIONS'],
            ),
            false
        );?>
    </div>
</div>