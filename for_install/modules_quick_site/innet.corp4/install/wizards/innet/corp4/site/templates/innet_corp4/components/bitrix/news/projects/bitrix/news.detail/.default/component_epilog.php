<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?if ($arParams['INNET_ALLOW_REVIEWS'] == 'Y'){?>
    <div class="tab">
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
                    1 => "",
                ),
                "PROPERTY_CODE" => array(
                    0 => "",
                    1 => "NAME_CLIENT",
                    2 => "COMMENT",
                    3 => "",
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
                "SHOW_404" => "N",
                "MESSAGE_404" => ""
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
                "EVENT_MESSAGE_TYPE" => "INNET_PROJECTS_REVIEWS",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_PROJECTS_REVIEWS_USER",
                "INNET_ID_ELEMENT" => $arResult["ID"],
                "INNET_NAME_ELEMENT" => $arResult["NAME"],
                "INNET_IBLOCK_ID_RECORD" => $arParams['INNET_IBLOCK_ID_REVIEWS'],
            ),
            false
        );?>
    </div>
<?}?>

<div id="order_projects" class="popwindow" data-title="" data-height="auto">
    <div class="popup-wrap1">
        <a class="bw_close"></a>
        <?$APPLICATION->IncludeComponent("innet:form", "order_projects", array(
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
                "EVENT_MESSAGE_TYPE" => "INNET_PROJECTS_ORDER",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_PROJECTS_ORDER_USER",
                "INNET_ID_ELEMENT" => $arResult["ID"],
                "INNET_NAME_ELEMENT" => $arResult["NAME"],
                "INNET_IBLOCK_ID_RECORD" => $arParams['INNET_IBLOCK_ID_ORDER'],
            ),
            false
        );?>
    </div>
</div>