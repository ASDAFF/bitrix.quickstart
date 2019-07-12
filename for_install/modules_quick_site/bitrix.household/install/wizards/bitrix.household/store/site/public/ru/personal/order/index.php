<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?><?$APPLICATION->IncludeComponent("bitrix:sale.personal.order", "list1", Array(
	"PROP_1" => array(	// Не показывать свойства для типа плательщика "Физическое лицо" (s1)
		0 => "5",
	),
	"PROP_2" => array(	// Не показывать свойства для типа плательщика "Юридическое лицо" (s1)
		0 => "18",
	),
	"SEF_MODE" => "Y",	// Включить поддержку ЧПУ
	"SEF_FOLDER" => "#SITE_DIR#personal/order/",	// Каталог ЧПУ (относительно корня сайта)
	"ORDERS_PER_PAGE" => "10",	// Количество заказов на одной странице
	"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",	// Страница подключения платежной системы
	"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",	// Страница с корзиной
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SAVE_IN_SESSION" => "N",	// Сохранять установки фильтра в сессии пользователя
	"NAV_TEMPLATE" => "arrows",	// Имя шаблона для постраничной навигации
	"SEF_URL_TEMPLATES" => array(
		"list" => "index.php",
		"detail" => "detail/#ID#/",
		"cancel" => "cancel/#ID#/",
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>