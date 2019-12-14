<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подписка на рассылку");
?><?$APPLICATION->IncludeComponent(
	"bitrix:subscribe.edit", 
	"monopoly", 
	array(
		"COMPONENT_TEMPLATE" => "monopoly",
		"SHOW_HIDDEN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"ALLOW_ANONYMOUS" => "Y",
		"SHOW_AUTH_LINKS" => "N",
		"SET_TITLE" => "N"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>