<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line", 
	"head", 
	array(
		"COMPONENT_TEMPLATE" => "head",
		"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
		"PATH_TO_ORDER" => "#SITE_DIR#personal/order/make/",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"SHOW_EMPTY_VALUES" => "N",
		"SHOW_PERSONAL_LINK" => "N",
		"PATH_TO_PERSONAL" => "#SITE_DIR#personal/",
		"SHOW_AUTHOR" => "N",
		"PATH_TO_REGISTER" => "#SITE_DIR#login/",
		"PATH_TO_PROFILE" => "#SITE_DIR#personal/",
		"SHOW_PRODUCTS" => "Y",
		"POSITION_FIXED" => "N",
		"HIDE_ON_BASKET_PAGES" => "N",
		"SHOW_DELAY" => "N",
		"SHOW_NOTAVAIL" => "N",
		"SHOW_SUBSCRIBE" => "N",
		"SHOW_IMAGE" => "N",
		"SHOW_PRICE" => "N",
		"SHOW_SUMMARY" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>