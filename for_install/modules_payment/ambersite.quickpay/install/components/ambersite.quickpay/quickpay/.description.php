<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PRIEM_PLATEGEI"),
	"DESCRIPTION" => GetMessage("MODUL_OSUCHESTVLYAET_PRIEM_PLATEGEJ_BANKOVSKIMI_KARTAMI"),
	"ICON" => "/images/main.getcode.gif",
	"SORT" => 1,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "ambersite",
		"NAME" => "AmberSite",
	),
	"AREA_BUTTONS" => array(
      array(
         'URL' => "javascript:window.open('/bitrix/admin/ambersite_quickpay_orders.php', '_blank');",
         'TITLE' => GetMessage("SPISOK_ZAKAZOV")
      ),
	),
);
?>