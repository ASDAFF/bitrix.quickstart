<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax",
	"",
	Array( 
		"PATH_TO_ORDER" => "/personal/order/make/",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_PERSONAL" => "/personal/order/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PATH_TO_AUTH" => "/auth/",
		"PAY_FROM_ACCOUNT" => "Y",
		"COUNT_DELIVERY_TAX" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"ALLOW_AUTO_REGISTER" => "N",
		"SEND_NEW_USER_NOTIFY" => "Y",
		"DELIVERY_NO_AJAX" => "N",
		"DELIVERY_NO_SESSION" => "N",
		"TEMPLATE_LOCATION" => "popup", 
		"SET_TITLE" => "Y",
		"PROP_1" => array(),
		"PROP_2" => array()
	)
);?>
<?if($_REQUEST['pay'] == 'Y'){
    ?>
<script>
$(function(){
    $('#pay').submit(); 
});</script>
<?
}?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>