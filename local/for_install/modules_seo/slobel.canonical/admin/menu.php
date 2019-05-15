<?
IncludeModuleLangFile(__FILE__);
if ($USER->IsAdmin())
{
	$menu = array(
		"parent_menu" => "global_menu_settings",
		"section" => "canonical",
		"sort" => 1,
		"text" => GetMessage("SLOBEL_MENU_ITEM"),
		"icon" => "slobel_canonical_menu_icon",
		"page_icon" => "slobel_canonical_menu_icon",
		"items_id" => "menu_canonical",
		"items" => array(),
	);
	$menu["items"][] = array(
			"text" => GetMessage("SLOBEL_MENU_LIST"),
			"url" => "slobel_canonical.php?lang=".LANGUAGE_ID,
			"more_url" => array(
					"slobel_canonical_edit.php",
			),
	);
	return $menu;
}
else
{
	return false;
}
?>