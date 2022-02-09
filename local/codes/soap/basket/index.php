<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><?
//$APPLICATION->IncludeComponent("bitrix:sale.basket.order.ajax", "basket_with_order", array(
//	"PATH_TO_PERSONAL" => "/user/profile/history/",
//	"PATH_TO_PAYMENT" => "payment.php",
//	"SEND_NEW_USER_NOTIFY" => "Y",
//	"COLUMNS_LIST" => array(
//		0 => "NAME",
//		1 => "QUANTITY",
//		2 => "DISCOUNT",
//		3 => "PRICE",
//		4 => "DELETE",
//	),
//	"HIDE_COUPON" => "Y",
//	"QUANTITY_FLOAT" => "N",
//	"PRICE_VAT_SHOW_VALUE" => "N",
//	"PRICE_TAX_SHOW_VALUE" => "N",
//	"SHOW_BUSKET_ORDER" => "Y",
//	"TEMPLATE_LOCATION" => "popup",
//	"SET_TITLE" => "Y"
//	),
//	false
//);
$APPLICATION->IncludeComponent("bitrix:eshop.sale.basket.basket", "basket_cm", Array(
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",	// Рассчитывать скидку для каждой позиции (на все количество товара)
	"COLUMNS_LIST" => array(	// Выводимые колонки
		0 => "NAME",
		1 => "PRICE",
		2 => "TYPE",
		3 => "QUANTITY",
		4 => "DELETE",
		5 => "DELAY",
		6 => "WEIGHT",
		7 => "DISCOUNT",
	),
	"PATH_TO_ORDER" => "/basket/order.php",	// Страница оформления заказа
	"HIDE_COUPON" => "N",	// Спрятать поле ввода купона
	"QUANTITY_FLOAT" => "N",	// Использовать дробное значение количества
	"PRICE_VAT_SHOW_VALUE" => "N",	// Отображать значение НДС
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	),
	false
);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>