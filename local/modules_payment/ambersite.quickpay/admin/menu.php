<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"sort" => 1,
	"text" => GetMessage("PRIEM_PLATEGEJ"),
	"icon" => "ambersite_quickpay_menu_icon",
	"page_icon" => "ambersite_quickpay_page_icon",
	"items_id" => "ambersite_quickpay",
	"items" => array(
		array(
			"text" => GetMessage("ZAKAZU"),
			"url" => "/bitrix/admin/ambersite_quickpay_orders.php",
			"more_url" => array('/bitrix/admin/ambersite_quickpay_orders_edit.php')
		)
	),
);
return $aMenu;
?> 
