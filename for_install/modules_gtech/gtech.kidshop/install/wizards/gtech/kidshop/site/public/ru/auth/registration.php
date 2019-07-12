<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?if($_GET["register"]=="yes"){
$APPLICATION->SetPageProperty("description", "Регистрация на сайте");
$APPLICATION->SetTitle("Регистрация на сайте");
$APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"",
	Array(
		"USER_PROPERTY_NAME" => "",
		"SEF_MODE" => "N",
		"SHOW_FIELDS" => array("NAME", "PERSONAL_PHONE"),
		"REQUIRED_FIELDS" => array("NAME"),
		"AUTH" => "Y",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array()
	),
false
);}?>
<?if($_GET["forgot_password"]=="yes"){
$APPLICATION->SetPageProperty("description", "Восстановление пароля");
$APPLICATION->SetTitle("Восстановление пароля");
$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "template1", Array(
	"USER_PROPERTY_NAME" => "",
	"SEF_MODE" => "N",
	"SHOW_FIELDS" => array(
		0 => "NAME",
		1 => "PERSONAL_PHONE",
	),
	"REQUIRED_FIELDS" => array(
		0 => "NAME",
	),
	"AUTH" => "Y",
	"USE_BACKURL" => "Y",
	"SUCCESS_PAGE" => "",
	"SET_TITLE" => "Y",
	"USER_PROPERTY" => ""
	),
	false
);}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>