<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>
<h1>Корзина</h1>	
<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "mm", array(
	"COLUMNS_LIST" => array(
	),
	"PATH_TO_ORDER" => SITE_DIR . "order/",
	"HIDE_COUPON" => "N",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"USE_PREPAYMENT" => "N",
	"QUANTITY_FLOAT" => "N",
	"SET_TITLE" => "Y",
	"ACTION_VARIABLE" => "action"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>