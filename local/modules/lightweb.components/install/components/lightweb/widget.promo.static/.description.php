<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LW_WIDGETS_PROMO_STATIC_NAME"),
	"DESCRIPTION" => GetMessage("LW_WIDGETS_PROMO_STATIC_DESC"),
	"ICON" => "/images/promo.gif",
	"PATH" => array(
		"ID" => "widget_list",
		"NAME" => GetMessage("LW_WIDGETS"),
		"SORT" => 10,
	),
);
?>