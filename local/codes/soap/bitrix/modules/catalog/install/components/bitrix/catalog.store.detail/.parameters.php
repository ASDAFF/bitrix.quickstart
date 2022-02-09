<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arMapType=array("Yandex","Google");

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"STORE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("STORE_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "1"
		),
		"MAP_TYPE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MAP_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arMapType,
			'DEFAULT' => "Yandex",
		),
		"CACHE_TIME" => Array("DEFAULT"=>"3600"),
		"SET_TITLE" => Array(),
	)
);
?>