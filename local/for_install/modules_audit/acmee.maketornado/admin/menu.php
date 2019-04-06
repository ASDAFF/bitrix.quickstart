<?
IncludeModuleLangFile(__FILE__);
$APPLICATION->AddHeadString("
<style>
.sys_menu_icon_maketornado {
    background: url(/bitrix/images/maketornado/logo.png) no-repeat 9px !important;
    background-image: url(/bitrix/images/maketornado/logo.png);
    background-repeat: no-repeat;
    background-position: 9px;
    }
</style>
");

if ($USER->IsAdmin())
{


	$menu = array(
		"parent_menu" => "global_menu_settings",
		"sort" => 1645,
		"text" => "maketornado",
		"icon" => "sys_menu_icon_maketornado",
		"page_icon" => "page_icon",
		"items_id" => "menu_maketornado",
		"items" => array(),
        "url" => "maketornado.php?lang=".LANGUAGE_ID
	);
	return $menu;
}
else
{
	return false;
}
?>