<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"FILENAME" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SA_FILENAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 'sitemap.xml',
		),
		"PRIORITY" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SA_PRIORITY"),
			"TYPE" => "STRING",
			"DEFAULT" => '0.5',
		),
	),
);
?>
