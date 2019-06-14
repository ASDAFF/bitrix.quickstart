<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("epages.pickpoint")!="D")
{
		$aMenu = array(
		"parent_menu" => "global_menu_store",
		"section" => "pickpoint",
		"sort" => 101,
		"text" => GetMessage("PP_TITLE"),
		"title" => GetMessage("PP_TITLE"),
		"icon" => "pickpoint_menu_icon",
		"page_icon" => "pickpoint_page_icon",
		"items_id" => "menu_pickpoint",
		"url" => "pickpoint_export.php?lang=".LANGUAGE_ID,
		"mode_url"=>Array(
			"url" => "pickpoint_export.php"
		),
		"items" => array(
		
		)
	);

	return $aMenu;
}
return false;
?>
