<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ASD_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["id"]}',
		),
		"ASD_TITLE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["title"]}',
		),
		"ASD_URL" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["url"]}',
		),
		"ASD_PICTURE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_PICTURE"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["picture"]}',
		),
		"ASD_TEXT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["text"]}',
		),
		"ASD_LINK_TITLE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_LINK_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("ASD_LINK_TITLE_DEF"),
		),
		"ASD_SITE_NAME" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_SITE_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ASD_INCLUDE_SCRIPTS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_INCLUDE_SCRIPTS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"REFRESH" => "Y",
			"VALUES" => array(
							"" => GetMessage("ASD_INCLUDE_SCRIPTS_NO"),
							"FB_LIKE" => GetMessage("ASD_INCLUDE_SCRIPTS_FB_LIKE"),
							"VK_LIKE" => GetMessage("ASD_INCLUDE_SCRIPTS_VK_LIKE"),
							"TWITTER" => GetMessage("ASD_INCLUDE_SCRIPTS_TWITTER"),
							"GOOGLE" => GetMessage("ASD_INCLUDE_SCRIPTS_GOOGLE"),
							"ASD_FAVORITE" => GetMessage("ASD_INCLUDE_SCRIPTS_ASD_FAVORITE"),
						)
		),
	),
);

if (!IsModuleInstalled("asd.favorite"))
	unset($arComponentParameters["PARAMETERS"]["ASD_INCLUDE_SCRIPTS"]["VALUES"]["ASD_FAVORITE"]);

if (is_array($arCurrentValues["ASD_INCLUDE_SCRIPTS"]) &&
	(in_array("FB_LIKE", $arCurrentValues["ASD_INCLUDE_SCRIPTS"]) || in_array("VK_LIKE", $arCurrentValues["ASD_INCLUDE_SCRIPTS"])))
{
	$arComponentParameters["PARAMETERS"]["LIKE_TYPE"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_LIKE_TYPE"),
														"TYPE" => "LIST",
														"VALUES" => array(
																		"LIKE" => GetMessage("ASD_LIKE_TYPE_LIKE"),
																		"RECOMMEND" => GetMessage("ASD_LIKE_TYPE_RECOMMEND"))
													);
}

if (is_array($arCurrentValues["ASD_INCLUDE_SCRIPTS"]) && in_array("VK_LIKE", $arCurrentValues["ASD_INCLUDE_SCRIPTS"]))
{
	$arComponentParameters["PARAMETERS"]["VK_API_ID"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_VK_API_ID"),
														"TYPE" => "STRING"
													);
	$arComponentParameters["PARAMETERS"]["VK_LIKE_VIEW"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_VK_LIKE_VIEW"),
														"TYPE" => "LIST",
														"DEFAULT" => "mini",
														"VALUES" => array(
																		"full" => GetMessage("ASD_VK_LIKE_VIEW_FULL"),
																		"button" => GetMessage("ASD_VK_LIKE_VIEW_BUTTON"),
																		"mini" => GetMessage("ASD_VK_LIKE_VIEW_MINI"))
													);
}

if (is_array($arCurrentValues["ASD_INCLUDE_SCRIPTS"]) && in_array("TWITTER", $arCurrentValues["ASD_INCLUDE_SCRIPTS"]))
{
	$arComponentParameters["PARAMETERS"]["TW_DATA_VIA"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_TW_DATA_VIA"),
														"TYPE" => "STRING"
													);
}

if (is_array($arCurrentValues["ASD_INCLUDE_SCRIPTS"]) && in_array("ASD_FAVORITE", $arCurrentValues["ASD_INCLUDE_SCRIPTS"]) && CModule::IncludeModule("asd.favorite"))
{
	$arTypes = array();
	$rsTypes = CASDfavorite::GetTypes();
	while ($arType = $rsTypes->GetNext())
		$arTypes[$arType["CODE"]] = $arType["NAME"];

	$arComponentParameters["PARAMETERS"] = array_merge($arComponentParameters["PARAMETERS"], array(
											"FAV_TYPE" => array(
												"PARENT" => "BASE",
												"NAME" => GetMessage("ASD_CMP_PARAM_FAV_TYPE"),

												"TYPE" => "LIST",
												"VALUES" => $arTypes,
											),
											"FAV_BUTTON_TYPE" => array(
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
										));
}

TrimArr($arCurrentValues["ASD_INCLUDE_SCRIPTS"]);

if (is_array($arCurrentValues["ASD_INCLUDE_SCRIPTS"]) && !empty($arCurrentValues["ASD_INCLUDE_SCRIPTS"]))
{
	$arComponentParameters["PARAMETERS"]["SCRIPT_IN_HEAD"] = Array(
														"PARENT" => "BASE",
														"NAME" => GetMessage("ASD_SCRIPT_IN_HEAD"),
														"TYPE" => "CHECKBOX"
													);
}
?>