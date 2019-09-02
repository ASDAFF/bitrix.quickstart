<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(

		"CACHE_TIME" => Array("DEFAULT" => 3600),

		"INCLUDE_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),

		"BOX_SIZE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_BOX_SIZE_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "100",
		),
		"IMAGE_WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IMAGE_WIDTH_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "",
		),
		"IMAGE_HEIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IMAGE_HEIGHT_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "",
		),
		"IMAGE_ALT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IMAGE_ALT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"IMAGE_TITLE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IMAGE_TITLE_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"ELEMENT_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_BOX_ELEMENT_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"IMAGE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IMAGE_ID_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"ZOOM" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_ZOOM_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "2",

		),

	),
);
?>