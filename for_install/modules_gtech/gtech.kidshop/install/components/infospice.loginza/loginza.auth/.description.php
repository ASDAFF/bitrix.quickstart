<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('LOGINZA_TITLE'),
	"DESCRIPTION" => GetMessage('LOGINZA_DESCR'),
	"ICON" => "/images/loginza.png",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "utility",
			"CHILD" => array(
				"ID" => "user",
				"NAME" => GetMessage('MAIN_USER_GROUP_NAME')
			),
	),
);
?>