<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"BLOCK_TITLE" => Array(
		"NAME" => GetMessage("BLOCK_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("DEFAULT_BLOCK_TITLE"),
	),
	"BLOCK_TYPE" => Array(
		"NAME" => GetMessage("BLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"connect" => GetMessage("CONNECT_BLOCK_TYPE"),
			"online" => GetMessage("ONLINE_BLOCK_TYPE"),
			"control" => GetMessage("CONTROL_BLOCK_TYPE")
		),
		"DEFAULT" => "connect",
	),
	"BLOCK_LINK" => Array(
		"NAME" => GetMessage("BLOCK_LINK"),
		"TYPE" => "TEXT",
		"DEFAULT" => "",
	),
);
?>
