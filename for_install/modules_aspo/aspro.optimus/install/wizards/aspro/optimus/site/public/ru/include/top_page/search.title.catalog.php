<?$APPLICATION->IncludeComponent("bitrix:search.title", "catalog", array(
	"NUM_CATEGORIES" => "1",
	"TOP_COUNT" => "5",
	"ORDER" => "date",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "Y",
	"SHOW_OTHERS" => "N",
	"PAGE" => SITE_DIR."catalog/",
	"CATEGORY_0_TITLE" => GetMessage("CATEGORY_PRODUСTCS_SEARCH_NAME"),
	"CATEGORY_0" => array(
		0 => "iblock_aspro_optimus_catalog",
	),
	"CATEGORY_0_iblock_aspro_optimus_catalog" => array(
		0 => "#IBLOCK_CATALOG_ID#",
	),
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "title-searchs-input",
	"CONTAINER_ID" => "title-search",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"PRICE_VAT_INCLUDE" => "Y",
	"SHOW_ANOUNCE" => "N",
	"PREVIEW_TRUNCATE_LEN" => "50",
	"SHOW_PREVIEW" => "Y",
	"PREVIEW_WIDTH" => "38",
	"PREVIEW_HEIGHT" => "38",
	"CONVERT_CURRENCY" => "N"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>