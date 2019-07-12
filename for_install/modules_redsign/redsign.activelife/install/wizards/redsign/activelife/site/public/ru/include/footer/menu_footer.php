<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"infooter",
	Array(
		"COMPONENT_TEMPLATE" => "infooter",
		"ROOT_MENU_TYPE" => "footer",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => Array(),
		"BLOCK_TITLE" => "Дополнительно",
        "CACHE_SELECTED_ITEMS" => "N",
	)
);?>