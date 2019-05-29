<?
if(\Bitrix\Main\ModuleManager::isModuleInstalled("mailtrig.events"))
{
	IncludeModuleLangFile(__FILE__);

	$arMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "mailtrig_events",
		"sort" => 50,
		"text" => GetMessage("MAILTRIG_EVENTS_MENU_MAIN"),
		"title" => GetMessage("MAILTRIG_EVENTS_MENU_MAIN_TITLE"),
		"icon" => "mailtrig_events_menu_icon",
		"page_icon" => "mailtrig_events_menu_icon",
		"module_id" => "mailtrig.events",
		"items_id" => "menu_mailtrig_events",
		"url" => "mailtrig_events_campaigns.php?lang=".LANGUAGE_ID,
		"more_url" => Array(
			"mailtrig_events_linechart.php",
			"mailtrig_events_results.php",
		),
		//"items" => array(),
	);
/*
	$aMenu['items'][] = array(
		"url" => "seo_sitemap.php?lang=".LANGUAGE_ID,
		"more_url" => array("seo_sitemap_edit.php?lang=".LANGUAGE_ID),
		"text" => GetMessage("SEO_MENU_SITEMAP_ALT"),
		//"title" => GetMessage("SEO_MENU_SITEMAP_ALT"),
	);
*/
	return $arMenu;
}

return false;
?>