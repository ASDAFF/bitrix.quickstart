<?php
IncludeModuleLangFile(__FILE__);

if($USER->IsAdmin()) {
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "ls_cs",
		"sort" => 550,
		"text" => GetMessage("LS_CS_MENU_HEAD"),
		"title"=> GetMessage("LS_CS_MENU_HEAD_ALT"),
		"icon" => "ls_cs_menu_icon",
		"page_icon" => "ls_cs_page_icon",
		"items_id" => "ls_cs_menu",
		"items" => array(
			array(
				"text" => GetMessage("LS_CS_MENU_ITEM_INVITE"),
				"url" => "ls_cs_invite.php?lang=".LANGUAGE_ID,
				"more_url" => array(),
				"title" => GetMessage("LS_CS_MENU_ITEM_INVITE_ALT")
			),
		)
	);

	return $aMenu;
}
return false;