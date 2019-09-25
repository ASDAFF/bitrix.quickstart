<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent(
	"krayt:sale.basket.basket", 
	".default", 
	array(
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DELETE",
			2 => "DELAY",
			3 => "TYPE",
			4 => "PRICE",
			5 => "QUANTITY",
			6 => "PROPERTY_EMARKET_PREVIEW_CH",
		),
		"OFFERS_PROPS" => array(
			0 => "EMARKET_SKU_MEMORY",
			1 => "EMARKET_SKU_COLOR",
		),
		"PATH_TO_ORDER" => SITE_DIR."personal/order/",
		"HIDE_COUPON" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "Y",
		"ACTION_VARIABLE" => "action"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>