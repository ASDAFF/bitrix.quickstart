<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"infootercatalog",
	array(
		"COMPONENT_TEMPLATE" => "infootercatalog",
		"ROOT_MENU_TYPE" => "infootercatalog",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"BLOCK_TITLE" => "Каталог",
		"LVL1_COUNT" => "8",
		"LVL2_COUNT" => "5",
		"ELLIPSIS_NAMES" => "Y",
        "CACHE_SELECTED_ITEMS" => "N",
	),
	false
);?>