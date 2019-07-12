<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Интернет-магазин мобильных телефонов и планшетов");
?><div class="catalog">
<?
$APPLICATION->IncludeComponent("bagmet:mobile.top", ".default", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "10",
	"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
	"OFFERS_LIMIT" => "5",
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "SORT",
		2 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
		2 => "",
	),
	"OFFERS_SORT_FIELD" => "sort",
	"OFFERS_SORT_ORDER" => "asc",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_top1",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "Y",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"CONVERT_CURRENCY" => "N",
	"OFFERS_CART_PROPERTIES" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
	),
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"DISPLAY_IMG_WIDTH" => "220",
	"DISPLAY_IMG_HEIGHT" => "260",
	"SHARPEN" => "2"
	),
	false
);
?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/small_banner1.php"), false);?>
<?
$APPLICATION->IncludeComponent("bagmet:mobile.top", ".default", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "6",
	"FLAG_PROPERTY_CODE" => "SALELEADER",
	"OFFERS_LIMIT" => "5",
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "SORT",
		2 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
		2 => "",
	),
	"OFFERS_SORT_FIELD" => "sort",
	"OFFERS_SORT_ORDER" => "asc",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_top2",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "Y",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"CONVERT_CURRENCY" => "N",
	"OFFERS_CART_PROPERTIES" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
	),
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"DISPLAY_IMG_WIDTH" => "220",
	"DISPLAY_IMG_HEIGHT" => "260",
	"SHARPEN" => "2"
	),
	false
);
?>
<?
$APPLICATION->IncludeComponent("bagmet:mobile.top", ".default", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "9",
	"FLAG_PROPERTY_CODE" => "NEWPRODUCT",
	"OFFERS_LIMIT" => "5",
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "SORT",
		2 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
		2 => "",
	),
	"OFFERS_SORT_FIELD" => "sort",
	"OFFERS_SORT_ORDER" => "asc",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_top3",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "Y",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"CONVERT_CURRENCY" => "N",
	"OFFERS_CART_PROPERTIES" => array(
		0 => "MEMORY_SIZE",
		1 => "GSM",
	),
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"DISPLAY_IMG_WIDTH" => "220",
	"DISPLAY_IMG_HEIGHT" => "260",
	"SHARPEN" => "2"
	),
	false
);
?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/small_banner2.php"), false);?>
</div>

<div class="news_block">
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "news_preview", array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
		),
		false
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "reviews_preview", array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "#REVIEWS_IBLOCK_ID#",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
		),
		false
	);?>
	<div class="splitter"></div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>