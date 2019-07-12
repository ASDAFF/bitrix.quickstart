<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?><?$APPLICATION->IncludeComponent(
	"bitrix:search.page",
	"flyaway",
	array(
		"COMPONENT_TEMPLATE" => "flyaway",
		"RESTART" => "Y",
		"NO_WORD_LOGIC" => "Y",
		"CHECK_DATES" => "N",
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"arrFILTER" => array(
		),
		"SHOW_WHERE" => "Y",
		"arrWHERE" => array(
		),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => "50",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"USE_LANGUAGE_GUESS" => "Y",
		"TEMPLATE_VIEW" => "showcase",
		"IBLOCK_ID" => "#IBLOCK_ID_catalog_catalog#",
		"USE_HOVER_POPUP" => "N",
		"RSFLYAWAY_PROP_SKU_MORE_PHOTO" => "MORE_PHOTO",
		"OFFER_TREE_PROPS" => array(
			0 => "COLOR_DIRECTORY",
			1 => "SKU_FASOVKA",
			2 => "SKU_SIZE",
		),
		"OFFER_TREE_COLOR_PROPS" => array(
			0 => "COLOR_DIRECTORY",
		),
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "flyaway",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR_DIRECTORY",
			1 => "SKU_FASOVKA",
			2 => "SKU_SIZE",
			3 => "",
		),
		"CONVERT_CURRENCY" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
