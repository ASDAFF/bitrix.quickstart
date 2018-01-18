<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("askaron.ibvote")!="D")
{
    CModule::IncludeModule('askaron.ibvote');
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "askaron.ibvote",
		"sort" => 50,
        "module_id" => "askaron.ibvote",
		"text" => GetMessage("ASKARON_IBVOTE_MENU_MAIN"),
		"title" => GetMessage("ASKARON_IBVOTE_MENU_MAIN_TITLE"),
		"url" => "askaron_ibvote_event_admin.php?lang=".LANGUAGE_ID,
		"icon" => "askaron_ibvote_menu_icon",
		"page_icon" => "askaron_ibvote_page_icon",
		"items_id" => "menu_askaron_ibvote",
		"items" => array(
			array(
				"text" => GetMessage("ASKARON_IBVOTE_MENU_EVENTS"),
				"url" => "askaron_ibvote_event_admin.php?lang=".LANGUAGE_ID,
				"more_url" => Array(
					"askaron_ibvote_event_admin.php"
				),
				"title" => GetMessage("ASKARON_IBVOTE_MENU_EVENTS_TITLE"),
			),		
		)
	);
	return $aMenu;
}
return false;
?>
