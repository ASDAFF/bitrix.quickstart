<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:search.page",
    "innet",
    array(
        "RESTART" => "Y",
        "NO_WORD_LOGIC" => "N",
        "CHECK_DATES" => "Y",
        "USE_TITLE_RANK" => "Y",
        "DEFAULT_SORT" => "rank",
        "FILTER_NAME" => "",
        "arrFILTER" => array(
            0 => "no",
        ),
        "SHOW_WHERE" => "Y",
        "arrWHERE" => array(),
        "SHOW_WHEN" => "N",
        "PAGE_RESULT_COUNT" => "25",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Результаты поиска",
        "PAGER_SHOW_ALWAYS" => "N",
        "USE_LANGUAGE_GUESS" => "Y",
        "USE_SUGGEST" => "Y",
        "SHOW_ITEM_TAGS" => "Y",
        "TAGS_INHERIT" => "N",
        "SHOW_ITEM_DATE_CHANGE" => "N",
        "SHOW_ORDER_BY" => "N",
        "SHOW_TAGS_CLOUD" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
    ),
    false
);?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>