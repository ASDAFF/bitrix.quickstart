<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"section" => "millcom_phpthumb",
	"sort" => 1,
	"text" => GetMessage("MILLCOM_PHPTHUMB_MODULE_NAME"),
	"title" => GetMessage("MILLCOM_PHPTHUMB_MODULE_NAME"),
	"url" => "millcom_phpthumb_list.php?lang=".LANGUAGE_ID,
	"icon" => "millcom_phpthumb_icon_1",
	"more_url" => Array("millcom_phpthumb_edit.php"),
	"page_icon" => "millcom_phpthumb_icon_2",
);
return $aMenu;
?>
