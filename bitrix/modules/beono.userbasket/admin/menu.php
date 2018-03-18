<?
IncludeModuleLangFile(__FILE__);

$module_access = $APPLICATION->GetGroupRight('sale');

if ($module_access>="U")
{
	$aMenu = array(
		"parent_menu" => "global_menu_store",
		"section" => "GENERAL",
		"sort" => 101,
		"text" => GetMessage('BEONO_MODULE_USERBASKET_MENU_TITLE'),
		"url"  => "/bitrix/admin/beono_userbasket.php?lang=".LANG,
		"title"=> GetMessage('BEONO_MODULE_USERBASKET_MENU_TITLE'),
		"icon" => "sale_menu_icon_orders",
		"page_icon" => "sale_page_icon_orders",
		"items_id" => "beono_userbasket"
	);
	return $aMenu;
}
?>