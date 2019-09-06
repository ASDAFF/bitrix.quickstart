<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_LIST" => Array(
			"NAME" => GetMessage("WS_SPPD_PATH_TO_LIST"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_DETAIL" => Array(
			"NAME" => GetMessage("WS_SPPD_PATH_TO_DETAIL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"ID" => Array(
			"NAME" => GetMessage("WS_SPPD_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "={\$ID}",
			"COLS" => 25,
		),
		'USE_AJAX_LOCATIONS' => array(
			'NAME' => GetMessage("WS_SPPD_USE_AJAX_LOCATIONS"),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SET_TITLE" => Array(),

	)
);
