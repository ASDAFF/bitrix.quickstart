<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Услуги");
?>

<div class="services">
    <?$APPLICATION->IncludeComponent(
        "bitrix:news",
        "services",
        array(
            "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
            "IBLOCK_ID" => "#INNET_IBLOCK_ID_SERVICES#",

            "INNET_IBLOCK_ID_ORDER" => "#INNET_IBLOCK_ID_SERVICES_ORDERS#",
            "INNET_IBLOCK_ID_QUESTIONS" => "#INNET_IBLOCK_ID_SERVICES_QUESTIONS#",
            "INNET_IBLOCK_ID_PROJECTS" => "#INNET_IBLOCK_ID_PROJECTS#",

            "SEF_FOLDER" => "#SITE_DIR#services/",
            "NEWS_COUNT" => "12",
            "USE_SEARCH" => "N",
            "USE_RSS" => "N",
            "USE_RATING" => "N",
            "USE_CATEGORIES" => "N",
            "USE_FILTER" => "N",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "",
            "SORT_ORDER2" => "",
            "CHECK_DATES" => "Y",
            "SEF_MODE" => "Y",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "SET_STATUS_404" => "Y",
            "SET_TITLE" => "Y",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "Y",
            "ADD_ELEMENT_CHAIN" => "Y",
            "USE_PERMISSIONS" => "N",
            "PREVIEW_TRUNCATE_LEN" => "",
            "LIST_ACTIVE_DATE_FORMAT" => "j F Y",
            "LIST_FIELD_CODE" => array(),
            "LIST_PROPERTY_CODE" => array(),
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "DISPLAY_NAME" => "N",
            "META_KEYWORDS" => "-",
            "META_DESCRIPTION" => "-",
            "BROWSER_TITLE" => "-",
            "DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
            "DETAIL_FIELD_CODE" => array(),
            "DETAIL_PROPERTY_CODE" => array(
                0 => "KEY_SERVICE",
            ),
            "DETAIL_DISPLAY_TOP_PAGER" => "N",
            "DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
            "DETAIL_PAGER_TITLE" => "",
            "DETAIL_PAGER_TEMPLATE" => "",
            "DETAIL_PAGER_SHOW_ALL" => "N",
            "PAGER_TEMPLATE" => "innet",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "COMPONENT_TEMPLATE" => "services",
            "SET_LAST_MODIFIED" => "N",
            "DETAIL_SET_CANONICAL_URL" => "N",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "SHOW_404" => "N",
            "MESSAGE_404" => "",
            "SEF_URL_TEMPLATES" => array(
                "news" => "",
                "section" => "#SECTION_CODE_PATH#/",
                "detail" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
            )
        ),
        false
    );?>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>