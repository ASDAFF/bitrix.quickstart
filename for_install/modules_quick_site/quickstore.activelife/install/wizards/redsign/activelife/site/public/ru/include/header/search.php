<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:search.title",
	"al",
	array(
		"COMPONENT_TEMPLATE" => "al",
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"ORDER" => "date",
		"USE_LANGUAGE_GUESS" => "N",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "N",
		"PAGE" => "#SITE_DIR#search/index.php",
		"CATEGORY_0_TITLE" => "",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"ACTION_TO" => "searchpage",
		"PROPCODE_MORE_PHOTO" => "MORE_PHOTO",
		"PROPCODE_SKU_MORE_PHOTO" => "SKU_MORE_PHOTO",
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "title-search",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"SKU_PRICE_SORT_ID" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "SKU_COLOR",
			1 => "SKU_SIZE",
			2 => "SKU_TKAN",
			3 => "",
		),
		"CONVERT_CURRENCY" => "N",
		"CATEGORY_0_iblock_catalog" => array(
			0 => "#CATALOG_IBLOCK_ID#",
		),
	),
	false
);?>