<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("sheepla.delivery")!="D")
{
		$aMenu = array(
		"parent_menu" => "global_menu_store",
		"section" => "sheepla",
		"sort" => 101,
		"text" => GetMessage("SHEEPLA_DETAILS"),
		"title" => GetMessage("SHEEPLA_DETAILS"),
		"icon" => "sheepla_menu_icon",
		"page_icon" => "sheepla_page_icon",
		"items_id" => "menu_sheepla",
		"url" => "sheepla_details.php?lang=".LANGUAGE_ID,
		"mode_url"=>Array(
			"url" => "sheepla_details.php"
		),
		"items" => array(
		
		)
	);

	return $aMenu;
}
return false;
?>
