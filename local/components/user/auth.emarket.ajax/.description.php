<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('AJAXAUTH_COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('AJAXAUTH_COMPONENT_DESCRIPTION'),
	"ICON" => "images/hl_detail.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "ajax_auth",
			"NAME" => GetMessage('AJAXAUTH_COMPONENT_CATEGORY_TITLE')
		),
	),
);

?>