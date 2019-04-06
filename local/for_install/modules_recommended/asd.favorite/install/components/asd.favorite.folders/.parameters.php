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
		"MAX_CHARS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_MAX_CHARS"),
			"TYPE" => "STRING",
			"COLS" => "5"
		),
		"FOLDER_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FOLDER_URL"),
			"TYPE" => "STRING",
		),
		"FOLDER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FOLDER_ID"),
			"TYPE" => "STRING",
		),
		"USER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_USER_ID"),
			"TYPE" => "STRING",
		),
	),
);
?>