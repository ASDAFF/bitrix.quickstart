<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки пользователя");
?><?$APPLICATION->IncludeComponent("bitrix:main.profile", "bejetstore", Array(
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"USER_PROPERTY"=>array('PERSONAL_PHOTO')
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>