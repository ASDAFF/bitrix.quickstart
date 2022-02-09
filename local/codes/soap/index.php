<?   
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Ноутбуки, ноутбук, нетбуки, нетбук, купить ноутбук, купить нетбуки, заказать ноутбук, купить нетбук, нетбук купить, купить нетбук в Москве, нетбук цены, куплю нетбук, нетбук цена, новый ноутбук, цены на ноутбуки, купить ноутбук недорого , ноутбук продажа , магазин ноутбук , ноутбук в магазине , купить хороший ноутбук, ноутбук дешево , ноутбук купить интернет магазин , заказать ноутбук , купить ноутбук интернет магазин , купить мини ноутбук , ноутбук Москва , купить ноутбук в интернет магазине , купить ноутбук через интернет , куплю ноутбук дешево , красивый ноутбук , ноутбук низкие цены , ноутбук в интернет магазине, комптую, комп2ю");
$APPLICATION->SetPageProperty("description", "Интернет-магазин ноутбуков Comp2You осуществляет продажу ноутбуков, нетбуков известных брендов. В ассортименте магазина ноутбуки и нетбуки, комплектующие к ним, аксессуары для ноутбуков, программное обеспечение, как для ноутбуков, так и для нетбуков. Низкие цены на ноутбуки, нетбуки, компьютеры. Подобрать, купить, заказать ноутбук или нетбук можно на сайте магазина. Продажа ноутбуков, нетбуков осуществляется в Москве (комптую, комп2ю)");
    $APPLICATION->SetTitle("Интернет-магазин ноутбуков Comp2You (комптую, комп2ю) осуществляет продажу ноутбуков, нетбуков известных брендов. В ассортименте магазина ноутбуки и нетбуки, комплектующие к ним, аксессуары для ноутбуков, программное обеспечение, как для ноутбуков, так и для нетбуков. Низкие цены на ноутбуки, нетбуки, компьютеры. Подобрать, купить, заказать ноутбук или нетбук можно на сайте магазина. Продажа ноутбуков, нетбуков осуществляется в Москве.");
?>             
<?$APPLICATION->IncludeComponent("cm:bigbanner.slider", ".default", array(
	"IBLOCK_TYPE" => "Information",
	"IBLOCK_ID" => "6",
	"NEWS_COUNT" => "5",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "link",
		1 => "",
	),
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
    "SET_TITLE"=>"N",
	"DISPLAY_DATE" => "N",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "N"
	),
	false
);?>
<?$APPLICATION->IncludeComponent("cm:catalog.section_sets", "template", array(
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => "5",
            "SECTION_ID" => "",
            "SECTION_CODE" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "",
                1 => "",
            ),
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_ORDER" => "asc",
            "FILTER_NAME" => "arrFilter",
            "INCLUDE_SUBSECTIONS" => "Y",
            "SHOW_ALL_WO_SECTION" => "N",
            "PAGE_ELEMENT_COUNT" => "30",
            "LINE_ELEMENT_COUNT" => "3",
            "PROPERTY_CODE" => array(
                0 => "group_good",
                1 => "",
            ),
            "OFFERS_LIMIT" => "5",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "BASKET_URL" => "/personal/basket.php",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "Y",
            "META_KEYWORDS" => "-",
            "META_DESCRIPTION" => "-",
            "BROWSER_TITLE" => "-",
            "ADD_SECTIONS_CHAIN" => "N",
            "DISPLAY_COMPARE" => "N",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "CACHE_FILTER" => "N",
            "PRICE_CODE" => array(
                0 => "price",  
            ),
            "USE_PRICE_COUNT" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_PROPERTIES" => array(
            ),
            "USE_PRODUCT_QUANTITY" => "N",
            "CONVERT_CURRENCY" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "Товары",
            "PAGER_SHOW_ALWAYS" => "Y",
            "PAGER_TEMPLATE" => "",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "Y",
            "AJAX_OPTION_ADDITIONAL" => ""
        ),
        false
    );?>
<hr class="b-hr" />
<?$APPLICATION->IncludeComponent("cm:slider", "template1", Array(
	"IBLOCK_TYPE" => "catalog",	// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID" => "1",	// Код информационного блока
	"PROPERTY_CODE" => array(	// Разделы баннеров
		0 => "actions",
		1 => "sale",
	)
	),
	false
);?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
