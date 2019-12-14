<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");?>

<?$APPLICATION->IncludeComponent(
	"bitrix:search.page", 
	"search", 
	array(
		"COMPONENT_TEMPLATE" => "search",
		"RESTART" => "N",
		"NO_WORD_LOGIC" => "N",
		"CHECK_DATES" => "Y",
		"USE_TITLE_RANK" => "Y",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"arrFILTER" => array(
			0 => "no",
		),
		"SHOW_WHERE" => "Y",
		"arrWHERE" => array(
			0 => "iblock_catalog",
			1 => "iblock_presscenter",
			2 => "iblock_shops",
		),
		"SHOW_WHEN" => "Y",
		"PAGE_RESULT_COUNT" => "50",
		"USE_FAVORITE" => "N",
		"ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "MORE_PHOTO",
		"OFFER_ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "SKU_MORE_PHOTO",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"USE_LANGUAGE_GUESS" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"IBLOCK_ID" => array(
			0 => "CATALOG_IBLOCK_ID",
		),
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "ONLINE",
		),
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DETAIL_PICTURE",
			2 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CONVERT_CURRENCY" => "Y",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "al",
		"USE_SUGGEST" => "Y",
		"SHOW_RATING" => "Y",
		"RATING_TYPE" => "like",
		"PATH_TO_USER_PROFILE" => ""
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>