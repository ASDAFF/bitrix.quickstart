<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мой профиль");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile", 
	".default", 
	array(
		"SET_TITLE" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"USER_PROPERTY" => array(
		),
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>