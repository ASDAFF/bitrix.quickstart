<?
global $USER;
if(!$USER->IsAdmin())
	return;
IncludeModuleLangFile(__FILE__);
$aMenu = array(
		"parent_menu" => "global_menu_content",
		"section" => "hlexim",
		"sort" => 360,
		"text" => GetMessage("HLEXIM_CONTROL"),
		"title"=> GetMessage("HLEXIM_CONTROL"),
		"icon" => "highloadblock_menu_icon",
		//"page_icon" => "forum_page_icon",
		"items_id" => "menu_hlexim",
		"url"=>"/bitrix/admin/hlexim_admin.php",
		"items" => array(
			array(
				"sort" => 10,
				"text"=> GetMessage("HLEXIM_CONTROL_EXPORT"),
				"title"=> GetMessage("HLEXIM_CONTROL_EXPORT"),
				"url"=>"/bitrix/admin/hlexim_admin.php",
				),
			array(
				"sort" => 20,
				"text"=> GetMessage("HLEXIM_CONTROL_IMPORT"),
				"title"=> GetMessage("HLEXIM_CONTROL_IMPORT"),
				"url"=>"/bitrix/admin/hlexim_admin_import.php",
				)
		)
	);
	return $aMenu;
?>