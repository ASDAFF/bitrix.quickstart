<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?>

<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order", "", array(
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "/personal/orders/",
	"ORDERS_PER_PAGE" => "10",
	"PATH_TO_PAYMENT" => "/personal/payment/",
	"PATH_TO_BASKET" => "/cart/",
	"SET_TITLE" => "Y",
	"SAVE_IN_SESSION" => "N",
	"NAV_TEMPLATE" => "arrows",
	"SEF_URL_TEMPLATES" => array(
		"list" => "index.php",
		"detail" => "detail/#ID#/",
		"cancel" => "cancel/#ID#/",
	),
	"SHOW_ACCOUNT_NUMBER" => "Y"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>