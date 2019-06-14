<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("asd.favorite"))
	return;

$arTypes = array();
$arFullTypes = array();
$rsTypes = CASDfavorite::GetTypes();
while ($arType = $rsTypes->GetNext())
{
	$arTypes[$arType["CODE"]] = $arType["NAME"];
	$arFullTypes[$arType["CODE"]] = $arType;
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"FAV_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FAV_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"REFRESH" => "Y"
		),
		"PREVIEW_WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PREVIEW_WIDTH"),
			"TYPE" => "STRING",
			"COLS" => "5",
			"DEFAULT" => "50"
		),
		"PREVIEW_HEIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PREVIEW_HEIGHT"),
			"TYPE" => "STRING",
			"COLS" => "5",
			"DEFAULT" => "50"
		),
		"PAGE_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PAGE_COUNT"),
			"TYPE" => "STRING",
			"COLS" => "5"
		),
		"PAGER_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PAGER_TEMPLATE"),
			"TYPE" => "STRING",
		),
		"USER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_USER_ID"),
			"TYPE" => "STRING",
		),
		"FOLDER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FOLDER_ID"),
			"TYPE" => "STRING",
		),
		"NOT_SHOW_WITH_NOT_FOLDER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_NOT_SHOW_WITH_NOT_FOLDER"),
			"TYPE" => "CHECKBOX",
		),
		"ALLOW_MOVED" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_ALLOW_MOVED"),
			"TYPE" => "CHECKBOX",
		),
	),
);

if (strlen($arCurrentValues["FAV_TYPE"]) && $arFullTypes[$arCurrentValues["FAV_TYPE"]]["MODULE"]=="blog")
{
	$arComponentParameters["PARAMETERS"]["URL_TO_BLOG_POST"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_CMP_PARAM_URL_TO_BLOG_POST"),
														"TYPE" => "STRING",
													);
}

if (strlen($arCurrentValues["FAV_TYPE"]) && $arFullTypes[$arCurrentValues["FAV_TYPE"]]["MODULE"]=="iblock")
{
	$arComponentParameters["PARAMETERS"]["URL_TO_ELEMENT"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_CMP_PARAM_URL_TO_ELEMENT"),
														"TYPE" => "STRING",
													);
}

if (strlen($arCurrentValues["FAV_TYPE"]) && $arFullTypes[$arCurrentValues["FAV_TYPE"]]["MODULE"]=="forum")
{
	$arComponentParameters["PARAMETERS"]["URL_TO_FORUM_POST"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_CMP_PARAM_URL_TO_FORUM_POST"),
														"TYPE" => "STRING",
														"DEFAULT" => "/forum/forum#forum_id#/topic#topic_id#/"
													);
	$arComponentParameters["PARAMETERS"]["URL_TO_FORUM_GROUP_POST"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_CMP_PARAM_URL_TO_FORUM_GROUP_POST"),
														"TYPE" => "STRING",
														"DEFAULT" => "/club/group/#group_id#/forum/#topic_id#/"
													);
	$arComponentParameters["PARAMETERS"]["URL_TO_FORUM_USER_POST"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_CMP_PARAM_URL_TO_FORUM_USER_POST"),
														"TYPE" => "STRING",
														"DEFAULT" => "/club/user/#user_id#/forum/#topic_id#/"
													);
}
?>