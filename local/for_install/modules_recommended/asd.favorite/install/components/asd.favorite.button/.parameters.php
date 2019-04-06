<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("asd.favorite"))
	return;

$arTypes = array();
$rsTypes = CASDfavorite::GetTypes();
while ($arType = $rsTypes->GetNext())
	$arTypes[$arType["CODE"]] = $arType["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"FAV_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FAV_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
		),
		"BUTTON_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_BUTTON_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array("fav" => GetMessage("ASD_CMP_PARAM_BUTTON_TYPE_FAV"), "lik" => GetMessage("ASD_CMP_PARAM_BUTTON_TYPE_LIK")),
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_ELEMENT_ID"),
			"TYPE" => "STRING",
		),
		"GET_COUNT_AFTER_LOAD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_GET_COUNT_AFTER_LOAD"),
			"TYPE" => "CHECKBOX",
		),
		"SET_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_SET_COUNT"),
			"TYPE" => "STRING",
		),
		"FAVED" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FAVED"),
			"TYPE" => "STRING",
		),
	    ),
);
?>