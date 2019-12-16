<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list",
	"al",
	array(
		"COMPONENT_TEMPLATE" => "al",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"DETAIL_URL" => "",
		"COMPARE_URL" => "#SITE_DIR#catalog/compare/",
		"NAME" => "CATALOG_COMPARE_LIST",
		"PROPCODE_MORE_PHOTO" => "MORE_PHOTO",
		"PROPCODE_SKU_MORE_PHOTO" => "SKU_MORE_PHOTO",
		"SKU_PRICE_SORT_ID" => "1",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>