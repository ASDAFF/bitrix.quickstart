<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arMapType=array("Yandex","Google");

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PHONE" => Array(
			"NAME" => GetMessage("SHOW_PHONE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SCHEDULE" => Array(
			"NAME" => GetMessage("SHOW_SCHEDULE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_ELEMENT" => array(
			'PARENT' => 'STORE_SETTINGS',
			'NAME' => GetMessage('STORE_PATH'),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "store/#store_id#",
		),
		"MAP_TYPE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MAP_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arMapType,
			'DEFAULT' => "Yandex",
		),
		"SET_TITLE" => Array(),
		"CACHE_TIME" => Array("DEFAULT"=>36000000),
	)
);
?>