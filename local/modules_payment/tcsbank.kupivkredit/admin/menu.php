<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("tcsbank.kupivkredit")!="D")
{
		$aMenu = array(
		"parent_menu" => "global_menu_store",
		"section" => "tcsbank",
		"sort" => 101,
		"text" => GetMessage("TCS_KUPIVKREDIT"),
		"title" => GetMessage("TCS_KUPIVKREDIT"),
		"icon" => "tcsbank_menu_icon",
		"page_icon" => "tcsbank_page_icon",
		"items_id" => "menu_tcsbank",
		"url" => "tcsbank_orders.php?lang=".LANGUAGE_ID,
		"mode_url"=>Array(
			"url" => "tcsbank_orders.php"
		),
		"items" => array(
		
		)
	);

	return $aMenu;
}
return false;
?>
