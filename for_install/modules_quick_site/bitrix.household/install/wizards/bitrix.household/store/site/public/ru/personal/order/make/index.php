<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?><?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "template1", Array(
	"PAY_FROM_ACCOUNT" => "Y",	// Позволять оплачивать с внутреннего счета
	"COUNT_DELIVERY_TAX" => "N",	// Рассчитывать налог для доставки
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",	// Рассчитывать скидку для каждой позиции (на все количество товара)
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",	// Позволять оплачивать с внутреннего счета только в полном объеме
	"ALLOW_AUTO_REGISTER" => "Y",	// Оформлять заказ с автоматической регистрацией пользователя
	"SEND_NEW_USER_NOTIFY" => "Y",	// Отправлять пользователю письмо, что он зарегистрирован на сайте
	"PROP_1" => "",	// Не показывать свойства для типа плательщика "Физическое лицо" (s1)
	"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",	// Страница корзины
	"PATH_TO_PERSONAL" => "#SITE_DIR#personal/order/",	// Страница персонального раздела
	"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",	// Страница подключения платежной системы
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"DELIVERY2PAY_SYSTEM" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>