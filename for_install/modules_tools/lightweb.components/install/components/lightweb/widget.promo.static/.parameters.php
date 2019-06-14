<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"NAME" => Array(
			"NAME" => GetMessage('LW_PROMO_NAME'),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"DESCRIPTION" => Array(
			"NAME" => GetMessage('LW_PROMO_DESCRIPTION'),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"NAME_KEY_ACTION" => Array(
			"NAME" => GetMessage('LW_PROMO_NAME_KEY_ACTION'),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"NAME_LINK_ACTION" => Array(
			"NAME" => GetMessage('LW_PROMO_NAME_LINK_ACTION'),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"FILE" => Array(
			"NAME" => GetMessage('LW_PROMO_FILE'),
			"TYPE" => "FILE",
			"PARENT" => "BASE",
			"FD_TARGET" => "F",
			"FD_EXT" => 'jpg,jpeg,png',
			"FD_UPLOAD" => true,
			"FD_USE_MEDIALIB" => true,
			"FD_MEDIALIB_TYPES" => array()
		),
	)
);


?>