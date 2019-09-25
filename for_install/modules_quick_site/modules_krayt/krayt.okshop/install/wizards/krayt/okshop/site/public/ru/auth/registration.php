<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("TITLE", "Универсальный интернет-магазин — OK-shop");
$APPLICATION->SetPageProperty("keywords", "Регистрация");
$APPLICATION->SetPageProperty("description", "Регистрация нового пользователя");
$APPLICATION->SetTitle("Регистрация");
?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"",
	Array(
		"USER_PROPERTY_NAME" => "",
		"SHOW_FIELDS" => array("NAME", "SECOND_NAME", "LAST_NAME", "PERSONAL_PHONE"),
		"REQUIRED_FIELDS" => array("NAME", "PERSONAL_PHONE"),
		"AUTH" => "Y",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array()
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>