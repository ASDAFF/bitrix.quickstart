<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.form",
	"fancybox",
	array(
		"COMPONENT_TEMPLATE" => "fancybox",
		"SHOW_ERRORS" => "Y",
		"AJAX_MODE" => "Y",
	),
	false
);?>