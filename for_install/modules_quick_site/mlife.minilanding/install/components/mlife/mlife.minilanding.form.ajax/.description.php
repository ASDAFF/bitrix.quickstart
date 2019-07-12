<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MLIFE_ML_AJAX_FORM"),
	"DESCRIPTION" => GetMessage("MLIFE_ML_AJAX_FORM_DESC"),
	"ICON" => "/images/icon_ajaxform.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => 'mlife_minilanding',
		"NAME" => GetMessage("MLIFE_ML_GROUP_NAME"),
	),
);

?>