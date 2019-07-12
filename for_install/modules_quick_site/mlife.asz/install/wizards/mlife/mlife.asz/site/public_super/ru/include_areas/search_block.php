<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"title", 
	array(
		"PAGE" => "#SITE_DIR#search/index.php",
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "Y",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "N",
		"CATEGORY_0_TITLE" => "Каталог",
		"CATEGORY_0" => array(
			0 => "iblock_#IBLOCK_TYPE#",
		),
		"CATEGORY_0_iblock_#IBLOCK_TYPE#" => array(
			0 => "#IBLOCK_ID#",
		),
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "title-search",
		"PRICE_CODE" => array(
			0 => "#PRICE_CODE#",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SHOW_PREVIEW" => "Y",
		"PREVIEW_WIDTH" => "40",
		"PREVIEW_HEIGHT" => "40"
	),
	false
);?>