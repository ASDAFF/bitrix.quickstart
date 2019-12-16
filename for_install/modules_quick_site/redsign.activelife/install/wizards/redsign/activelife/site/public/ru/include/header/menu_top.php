<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"toppanel",
	Array(
		"COMPONENT_TEMPLATE" => "toppanel",
		"ROOT_MENU_TYPE" => "toppanel",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => Array(),
        "CACHE_SELECTED_ITEMS" => "N",
	)
);?>