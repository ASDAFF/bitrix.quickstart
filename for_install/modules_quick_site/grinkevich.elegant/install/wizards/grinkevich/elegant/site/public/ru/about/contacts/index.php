<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<p><strong>Контактные телефоны</strong></p>

<p>Телефон в Санкт-Петербурге &mdash; (812) 123-45-67</p>

<p>Телефон в Москве — (495) 123-45-67</p>

<p>Федеральный бесплатный телефон — 8-800-123-45-67</p>

<p><strong>Адрес</strong></p>

<p>190000, Москва, ул. Центральная, д.1А, офис 2Б</p>

<p><?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:23.0;s:10:\"yandex_lon\";d:32.0;s:12:\"yandex_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:23.0;s:3:\"LAT\";d:32.0;s:4:\"TEXT\";s:6:\"Магазин Womanizer\";}}}",
		"MAP_WIDTH" => "600",
		"MAP_HEIGHT" => "500",
		"CONTROLS" => array("ZOOM", "MINIMAP", "TYPECONTROL", "SCALELINE"),
		"OPTIONS" => array("ENABLE_SCROLL_ZOOM", "ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING"),
		"MAP_ID" => ""
	)
);?></p>



<h2>Форма обратной связи</h2>

<p><?$APPLICATION->IncludeComponent("bitrix:main.feedback", "feedback", Array(
	"USE_CAPTCHA" => "Y",	// Использовать защиту от автоматических сообщений (CAPTCHA) для неавторизованных пользователей
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",	// Сообщение, выводимое пользователю после отправки
	"EMAIL_TO" => "nr@artw.ru",	// E-mail, на который будет отправлено письмо
	"REQUIRED_FIELDS" => array(	// Обязательные поля для заполнения
		0 => "NAME",
		1 => "EMAIL",
		2 => "MESSAGE",
	),
	"EVENT_MESSAGE_ID" => "",	// Почтовые шаблоны для отправки письма
	),
	false
);?></p>


 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>