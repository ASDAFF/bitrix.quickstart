    <div class="col-md-3">
        <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "", Array(
                    "IBLOCK_TYPE"    =>    $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID"    =>    $arParams["IBLOCK_ID"],
                    "SECTION_ID" => '',    
                    "SECTION_CODE" => "",    
                    "COUNT_ELEMENTS" => "Y",    
                    "TOP_DEPTH" => "1",    
                    "CACHE_TYPE"    =>    $arParams["CACHE_TYPE"],
                    "CACHE_TIME"    =>    $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "ADD_SECTIONS_CHAIN" => "N",    
                ),
                $component
            );?>
        <?$APPLICATION->IncludeComponent(
                "bitrix:news.line",
                "",
                Array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCKS" => array($arParams["IBLOCK_ID"]),
                    "NEWS_COUNT" => "5",
                    "FIELD_CODE" => array("", ""),
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "TIMESTAMP_X",
                    "SORT_ORDER2" => "DESC",
                    "DETAIL_URL" => "",
                    "CACHE_TYPE"    =>    $arParams["CACHE_TYPE"],
                    "CACHE_TIME"    =>    $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "ACTIVE_DATE_FORMAT" => "M j, Y"
                ),
                $component
            );?>

        <!-- !RECENT POSTS WIDGET -->
    </div>