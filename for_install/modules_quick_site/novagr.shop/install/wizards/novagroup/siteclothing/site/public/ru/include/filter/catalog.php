<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"novagr.shop:universal.filter",
	"default",
	Array(
		"CATALOG_IBLOCK_TYPE" => "catalog",
		"CATALOG_IBLOCK_CODE" => "novagr_standard_products",
		"ROOT_PATH" => SITE_DIR."catalog/",
		"TRADEOF_IBLOCK_TYPE" => "offers",
		"TRADEOF_IBLOCK_CODE" => "novagr_standard_products_offers",
		"SPECIAL_SORT_ORDER" => "-5",
		"SECTION_SORT_ORDER" => "-20",
		"PRICE_SORT_ORDER" => "-10",
		"SHOW_PRICE_SLIDER" => "Y",
		"SHOW_SECTION" => "N",
		"BRAND_ROOT" => SITE_DIR."brand/",
		"FASHION_MODE" => "N",
		"FASHION_ROOT" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "2592000"
	)
);?>