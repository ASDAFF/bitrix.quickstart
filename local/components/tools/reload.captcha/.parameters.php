<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"MAIN_PARAMS" => array(
			"NAME" => GetMessage("MAIN_PARAMS"),
			"SORT" => "100"
			),
		"ADDITIONAL_PARAMS" => array(
			"NAME" => GetMessage("ADDITIONAL_PARAMS"),
			"SORT" => "110"
			)
		),
	"PARAMETERS" => array( 
		"USE_GLOBAL" => Array(
			"PARENT" => "MAIN_PARAMS",
			"NAME" => GetMessage("C_USE_GLOBAL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"FORM_NAME" => array(
			"PARENT" => "MAIN_PARAMS",
			"NAME" => GetMessage("C_FORM_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "Y",
			"VALUES" => array(),
			"ADDITIONAL_VALUES" => "Y",
		),
		"IMAGE_DIALOG" => Array(
			"PARENT" => "ADDITIONAL_PARAMS",
			"NAME" => GetMessage("IMAGE_DIALOG"),
			"TYPE" => "FILE",
			"DEFAULT" => "/bitrix/components/bitrix/reload.captcha/templates/.default/images/reload.png"
		)
	)
);
?>