<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_NAME"),
	"DESCRIPTION" => GetMessage("T_DESCRIPTION"),
	"ICON" => "/images/mail_antispam.gif",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "iesa",
			"NAME" => GetMessage("T_NAME_CATEGORY"),
			"SORT" => 100,
		)
	),
);

?>