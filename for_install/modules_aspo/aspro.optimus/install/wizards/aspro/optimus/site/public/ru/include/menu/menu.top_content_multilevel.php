<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"top_content_multilevel", 
	array(
		"ROOT_MENU_TYPE" => "top_content_multilevel",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => \Bitrix\Main\Config\Option::get("aspro.optimus", "MAX_DEPTH_MENU", 2),
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
	),
	false
);?>