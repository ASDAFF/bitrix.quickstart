<?

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 
?>
<script src="/js/js.js"></script>
<?
    if(CModule::IncludeModule("iblock")) 
    { 


        $param=$APPLICATION->IncludeComponent("cm:catalog.smart.filter", "compare", array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "1",
                "SECTION_ID" => $_REQUEST["section_id"],
                "FILTER_NAME" => "arrFilter",
                "CACHE_TYPE" => "N",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "SAVE_IN_SESSION" => "Y",
                "PRICE_CODE" => array(
                )
            ),
            false
        );

        $APPLICATION->IncludeComponent(
            "cm:catalog.filter",
            "",
            Array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "1",
                "FILTER_NAME" => "arrFilter",
                "PROPERTY_CODE" => $param,
                "PRICE_CODE" => array(0 => "price", 1 => "clearing"),

            ),
            $component
        );
        
                
            if($_REQUEST["temp"] == "grid"){
                $list_temp = "grid";
            }else{
                $list_temp = "";
            }
        
        
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            $list_temp,
            Array(
                "AJAX_MODE" => "N",
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "1",
                "SECTION_ID" => $_REQUEST["section_id"],
                "SECTION_CODE" => "",
                "SECTION_USER_FIELDS" => array(),
                "ELEMENT_SORT_FIELD" => "sort",
                "ELEMENT_SORT_ORDER" => "asc",
                "USE_FILTER" => "Y",
                "FILTER_NAME" => "arrFilter",
                "INCLUDE_SUBSECTIONS" => "Y",
                "SHOW_ALL_WO_SECTION" => "Y",
                "SECTION_URL" => "",
                "DETAIL_URL" => "",
                "BASKET_URL" => "/personal/basket.php",
                "ACTION_VARIABLE" => "action",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "META_KEYWORDS" => "-",
                "META_DESCRIPTION" => "-",
                "BROWSER_TITLE" => "-",
                "ADD_SECTIONS_CHAIN" => "Y",
                "DISPLAY_COMPARE" => "N",
                "SET_TITLE" => "Y",
                "SET_STATUS_404" => "N",
                "PAGE_ELEMENT_COUNT" => "30",
                "LINE_ELEMENT_COUNT" => "3",
                "PROPERTY_CODE" => $param,
                "PRICE_CODE" => array(0 => "price", 1 => "clearing"),
                "USE_PRICE_COUNT" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_PROPERTIES" => array(),
                "USE_PRODUCT_QUANTITY" => "Y",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_FILTER" => "Y",
                "CACHE_GROUPS" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Товары",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "CONVERT_CURRENCY" => "N",
                "CURRENCY_ID" => "RUB",
            )
        );
}?>