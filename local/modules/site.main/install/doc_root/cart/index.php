<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>

<?$APPLICATION->IncludeComponent(
	"site:sale.order.ajax", 
	".default", 
	array(
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"DELIVERY_NO_AJAX" => "N",
		"PAY_FROM_ACCOUNT" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"USE_PREPAYMENT" => "N",
		"SEND_NEW_USER_NOTIFY" => "Y",
		"SET_TITLE" => "Y",
		"PATH_TO_BASKET" => "",
		"PATH_TO_CATALOG" => "/catalog/",
		"DISABLE_BASKET_REDIRECT" => "Y",
		"DELIVERY_GROUPS" => array(
			0 => "2",
		),
		"PATH_TO_PERSONAL" => "/personal/",
		"PATH_TO_ORDERS_LIST" => "/personal/orders/",
		"PATH_TO_PAYMENT" => "/personal/payment/",
		"FEEDBACK_PHONE" => "8 800 856-77-77",
		"PATH_TO_FEEDBACK_FORM" => "/ajax/form/callback/",
		"PAY_SYSTEMS_ONLINE" => array(
			0 => "4",
		)
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>