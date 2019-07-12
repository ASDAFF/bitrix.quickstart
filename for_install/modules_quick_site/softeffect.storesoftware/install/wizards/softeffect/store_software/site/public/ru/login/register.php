<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent("bitrix:main.register", "template1", Array(
	"SHOW_FIELDS" => array(	// Поля, которые показывать в форме
		0 => "NAME",
		1 => "SECOND_NAME",
		2 => "LAST_NAME",
		3 => "PERSONAL_PHONE",
	),
	"REQUIRED_FIELDS" => "",	// Поля, обязательные для заполнения
	"AUTH" => "Y",	// Автоматически авторизовать пользователей
	"USE_BACKURL" => "Y",	// Отправлять пользователя по обратной ссылке, если она есть
	"SUCCESS_PAGE" => "",	// Страница окончания регистрации
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"USER_PROPERTY" => "",	// Показывать доп. свойства
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>