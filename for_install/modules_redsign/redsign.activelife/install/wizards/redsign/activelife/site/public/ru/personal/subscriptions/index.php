<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Подписка на рассылку");
$APPLICATION->SetTitle("Подписка на рассылку");
?>
<?$APPLICATION->IncludeComponent("bitrix:subscribe.edit", "al", array(
	"SHOW_HIDDEN" => "N",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"ALLOW_ANONYMOUS" => "Y",
	"SHOW_AUTH_LINKS" => "Y",
	"SET_TITLE" => "N",
	"USER_PROPERTY_NAME" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>