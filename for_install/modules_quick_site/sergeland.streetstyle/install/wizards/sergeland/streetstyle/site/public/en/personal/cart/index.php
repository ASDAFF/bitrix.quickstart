<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Basket");
?>
<?$APPLICATION->IncludeComponent("sergeland:sale.basket.basket","big", array(
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	"COLUMNS_LIST" => array(
		0 => "NAME",
		1 => "PROPS",
		2 => "PRICE",
		3 => "QUANTITY",
		4 => "DELETE",
		5 => "DISCOUNT",
	),
	"PATH_TO_ORDER" => "#SITE_DIR#personal/order/make/",
	"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
	"HIDE_COUPON" => "N",
	"QUANTITY_FLOAT" => "N",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"USE_PREPAYMENT" => "N",
	"SET_TITLE" => "Y"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>