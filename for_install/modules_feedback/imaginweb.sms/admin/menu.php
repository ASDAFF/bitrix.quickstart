<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"section" => "imaginweb.sms",
	"sort" => 200,
	"text" => GetMessage("mnu_sect"),
	"title" => GetMessage("mnu_sect_title"),
	"url" => "imaginweb.sms_subscr_index.php?lang=".LANGUAGE_ID,
	"icon" => "iwebsms_subscribe_menu_icon",
	"page_icon" => "iwebsms_subscribe_page_icon",
	"items_id" => "menu_imaginweb.sms",
	"items" => array(
		array(
			"text" => GetMessage("mnu_posting"),
			"url" => "imaginweb.sms_posting_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("imaginweb.sms_posting_edit.php"),
			"title" => GetMessage("mnu_posting_alt")
		),
		array(
			"text" => GetMessage("mnu_subscr"),
			"url" => "imaginweb.sms_subscr_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("imaginweb.sms_subscr_edit.php"),
			"title" => GetMessage("mnu_subscr_alt")
		),
		array(
			"text" => GetMessage("mnu_subscr_import"),
			"url" => "imaginweb.sms_subscr_import.php?lang=".LANGUAGE_ID,
			"more_url" => array("imaginweb.sms_subscr_import.php"),
			"title" => GetMessage("mnu_subscr_import_alt")
		),
		array(
			"text" => GetMessage("mnu_rub"),
			"url" => "imaginweb.sms_rubric_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("imaginweb.sms_rubric_edit.php", "imaginweb.sms_template_test.php"),
			"title" => GetMessage("mnu_rub_alt")
		)
	)
);

return $aMenu;

?>