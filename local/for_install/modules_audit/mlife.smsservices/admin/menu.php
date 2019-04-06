<?
IncludeModuleLangFile(__FILE__);

$aMenu = Array(
	"parent_menu" => "global_menu_marketing",
		"section" => "mlife.smsservices",
		"sort" => 100,
		"module_id" => "mlife.smsservices",
		"text" => GetMessage("MLIFESS_MENU_MODULE_NAME"),
		"title" => GetMessage("MLIFESS_MENU_MODULE_DESC"),
		"items_id" => "menu_smsservices",
		"icon" => "fileman_sticker_icon",
		"items" => array(
			array(
				"text" => GetMessage("MLIFESS_MENU_SENSMS"),
				"url" => "mlife_smsservices_sendform.php?lang=".LANGUAGE_ID,
				"more_url" => Array(),
				"title" => GetMessage("MLIFESS_MENU_SENSMS")
			),
			array(
				"text" => GetMessage("MLIFESS_MENU_BALANCE"),
				"url" => "mlife_smsservices_balance.php?lang=".LANGUAGE_ID,
				"more_url" => Array(),
				"title" => GetMessage("MLIFESS_MENU_BALANCE")
			),
			array(
				"text" => GetMessage("MLIFESS_MENU_HISTORY"),
				"url" => "mlife_smsservices_list.php?lang=".LANGUAGE_ID,
				"more_url" => Array(),
				"title" => GetMessage("MLIFESS_MENU_HISTORY")
			),
			array(
				"text" => GetMessage("MLIFESS_MENU_EVENTLIST"),
				"url" => "mlife_smsservices_eventlist.php?lang=".LANGUAGE_ID,
				"more_url" => Array('mlife_smsservices_eventlist_edit.php?lang='.LANGUAGE_ID),
				"title" => GetMessage("MLIFESS_MENU_EVENTLIST")
			)
		)
);
return $aMenu;
?>
