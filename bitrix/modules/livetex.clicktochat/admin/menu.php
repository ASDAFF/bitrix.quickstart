<?
IncludeModuleLangFile(__FILE__);
$APPLICATION->AddHeadString("
<style>
.sys_menu_icon_livetex {
    background: url(/upload/livetex/livetex_16x16.png) no-repeat 9px !important;
    background-image: url(/upload/livetex/livetex_16x16.png);
    background-repeat: no-repeat;
    background-position: 9px;
    }
</style>
");
if ($USER->IsAdmin())
{
	$menu = array(
		"parent_menu" => "global_menu_settings",
		"section" => "livetex",
		"sort" => 1645,
		"text" => GetMessage("LIVE_MODULE_NAME"),
		"icon" => "sys_menu_icon_livetex",
		"page_icon" => "page_icon",
		"items_id" => "menu_livetex",
		"items" => array(),
        "url" => "livetex.php?lang=".LANGUAGE_ID
	);
	return $menu;
}
else
{
	return false;
}
?>