<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?>
<div class="contentclose contenttext nomart" id="basket">
<?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "order-list-mini", array(
	"PAY_FROM_ACCOUNT" => "N",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_AJAX" => "Y",
	"PROP_1" => array(
		0 => "4",
	),
	"PROP_2" => array(
		0 => "15",
		1 => "16",
		2 => "17",
	),
	"PATH_TO_BASKET" => "#SITE_DIR#basket/",
	"PATH_TO_PERSONAL" => "#SITE_DIR#personal/order/",
	"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",
	"PATH_TO_AUTH" => "#SITE_DIR#auth/",
	"SET_TITLE" => "Y"
	),
	false
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>