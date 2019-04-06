<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("itena.panel")!="D") 
{
	$aMenu = array	(
					"parent_menu" => "global_menu_content",
					"sort" => 300,
					"text" => GetMessage("PANEL_MENU_MAIN"),
					"title" => GetMessage("PANEL_MENU_MAIN_TITLE"),
					"url" => "panel_comments.php?lang=".LANGUAGE_ID,
					"icon" => "panel_menu_icon",
					"page_icon" => "panel_page_icon",
					);

	return $aMenu;
}
return false;
?>