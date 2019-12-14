<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list", 
	"al", 
	array(
		"COMPONENT_TEMPLATE" => "al",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"POSITION_FIXED" => "Y",
		"POSITION" => "top right",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"DETAIL_URL" => "",
		"COMPARE_URL" => "#SITE_DIR#catalog/compare/",
		"NAME" => "CATALOG_COMPARE_LIST",
		"ACTION_VARIABLE" => "action_cl",
		"PRODUCT_ID_VARIABLE" => "id",
		"ADDITIONAL_PICT_PROP" => "MORE_PHOTO",
		"OFFER_ADDITIONAL_PICT_PROP" => "SKU_MORE_PHOTO",
		"SKU_PRICE_SORT_ID" => "0"
	),
	false
);?>