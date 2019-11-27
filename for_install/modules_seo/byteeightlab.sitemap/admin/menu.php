<?
IncludeModuleLangFile(__FILE__);
if(
	CModule::IncludeModule('byteeightlab.sitemap')
	&& $APPLICATION->GetGroupRight("byteeightlab.sitemap") != "D"
)
{
	$aMenu = Array(
		array(
			"parent_menu" => "global_menu_content",
			"sort" => 100,
			"text" => GetMessage("BEL_MENU_NAME"),
			"title"=>GetMessage("BEL_MENU_NAME"),
			"url" => "byteeightlab_sitemap_generator.php?lang=".LANGUAGE_ID,
			"icon" => "byteeightlab_sitemap_menu_icon",
			"page_icon" => "byteeightlab_sitemap_menu_icon",
			"items_id" => "byteeightlab_sitemap",
		),
	);
	return $aMenu;
}
return false;
?>
