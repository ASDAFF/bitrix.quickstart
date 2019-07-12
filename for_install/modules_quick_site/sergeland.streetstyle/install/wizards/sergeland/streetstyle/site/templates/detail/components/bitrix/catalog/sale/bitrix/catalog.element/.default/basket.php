<?if ( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) ) 
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

header("Cache-Control: no-store, no-cache, must-revalidate");

$APPLICATION->IncludeComponent("sergeland:sale.basket.basket", "ajax", array(

			"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
			"COLUMNS_LIST" => array(
				0 => "NAME",
				1 => "PROPS",
				2 => "PRICE",
				3 => "QUANTITY",
				4 => "DELETE",
			),
			"PATH_TO_ORDER" => "#SITE_DIR#personal/order/make/",
			"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",			
			"HIDE_COUPON" => "Y",
			"QUANTITY_FLOAT" => "N",
			"PRICE_VAT_SHOW_VALUE" => "N",
			"USE_PREPAYMENT" => "N",
			"SET_TITLE" => "N"
			),
			false
);
?>