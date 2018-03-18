<?php
/*.require_module 'bitrix_main_include_prolog_admin_before';.*/
IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin())
	return false;

if (CModule::IncludeModuleEx('karudo.vcs') == MODULE_DEMO_EXPIRED) {
	return false;
}

$arMenu = array(
	"parent_menu" => "global_menu_services",
	"section" => "karudo_vcs",
	"sort" => 3000,
	"text" => GetMessage("VCS_MENU_ITEM"),
	"title" => GetMessage("VCS_MENU_TITLE"),
	"url" => "karudo.chitems_list.php?lang=".LANGUAGE_ID,
	"icon" => "vcs_admin_menu_icon",
	'items_id' => 'menu_karudo_vcs',
	"items" => array(
		array(
			"text" => GetMessage("VCS_CHECK_FOR_NEW"),
			"url" => "karudo.chitems_list.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("VCS_CHECK_FOR_NEW_ALT")
		),
		array(
			"text" => GetMessage("VCS_REPOSITORY_ITEMS"),
			"url" => "karudo.items_list.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("VCS_REPOSITORY_ITEMS_ALT"),
			"more_url" => array("karudo.item_revisions_list.php"),
		),
		array(
			"text" => GetMessage("VCS_REVISIONS"),
			"url" => "karudo.revisions_list.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("VCS_REVISIONS_ALT")
		),
	)

);

if (CVCSConfig::GetDriversInMenu()) {
	$arMenu['items'][] = array(
		"text" => GetMessage("VCS_MENU_DRIVERS"),
		"url" => "karudo.drivers_list.php?lang=".LANGUAGE_ID,
		"title" => GetMessage("VCS_MENU_DRIVERS_ALT"),
		"more_url" => array("karudo.driver_edit.php"),
	);
}

return $arMenu;
?>
