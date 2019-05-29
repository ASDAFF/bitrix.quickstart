<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("all" => " "));
$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"][0] != "all" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"ELEMENT_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BMS_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BMS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "catalog",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BMS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '1',
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		),
		"DEPTH_LEVEL" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BMS_DEPTH_LEVEL"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"ONLY_SECTIONS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BMS_ONLY_SECTIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
		),
		"CACHE_TIME" => Array("DEFAULT" => 36000000),
	),
);
if(count($arCurrentValues["IBLOCK_TYPE"]) > 0 && $arCurrentValues["IBLOCK_TYPE"][0] != 'all') {
	$arCategories = Array();
	foreach($arCurrentValues["IBLOCK_TYPE"] as $val)
	{
		if(array_key_exists($val, $arTypesEx))
			$arCategories[] = Array(
				"TITLE" => $arTypesEx[$val],
				"KEY" => $val
			);
	}
	foreach($arCategories as $k => $i)
	{
		$arComponentParameters["GROUPS"]["CATEGORY_".$i["KEY"]] = array(
			"NAME" => GetMessage("CP_BST_NUM_CATEGORY", array("#TITLE#" => $arCategories[$k]["TITLE"]))
		);
		$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_LINK"] = array(
			"PARENT" => "CATEGORY_".$i["KEY"],
			"NAME" => GetMessage("CP_BST_CATEGORY_TITLE").' ('.GetMessage("CP_BMS_SEF_BASE_URL").')',
			"TYPE" => "STRING",
		);
		$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_IS_SEF"] = array(
			"PARENT" => "CATEGORY_".$i["KEY"],
			"NAME" => GetMessage("CP_BMS_IS_SEF"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		);
		if($arCurrentValues["CATEGORY_".$i["KEY"]."_IS_SEF"] == 'Y') {
			$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_SECTION_LINK"] = CIBlockParameters::GetPathTemplateParam(
				"SECTION",
				"SECTION_PAGE_URL",
				GetMessage("CP_BMS_SECTION_PAGE_URL"),
				"#SECTION_CODE#/",
				"CATEGORY_".$i["KEY"]
			);
			$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_DETAIL_LINK"] = CIBlockParameters::GetPathTemplateParam(
				"DETAIL",
				"DETAIL_PAGE_URL",
				GetMessage("CP_BMS_DETAIL_PAGE_URL"),
				"#SECTION_CODE#/#ELEMENT_ID#/",
				"CATEGORY_".$i["KEY"]
			);
		}
	}
}
if((count($arCurrentValues["IBLOCK_ID"]) > 0) && (count($arCurrentValues["IBLOCK_TYPE"]) <= 0 || $arCurrentValues["IBLOCK_TYPE"][0] == 'all')) {
	$arCategories = Array();
	foreach($arCurrentValues["IBLOCK_ID"] as $val)
	{
		if(array_key_exists($val, $arIBlocks))
			$arCategories[] = Array(
				"TITLE" => $arIBlocks[$val],
				"KEY" => $val
			);
	}
	foreach($arCategories as $k => $i)
	{
		$arComponentParameters["GROUPS"]["CATEGORY_".$i["KEY"]] = array(
			"NAME" => GetMessage("CP_BST_NUM_CATEGORY", array("#TITLE#" => $arCategories[$k]["TITLE"]))
		);
		$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_IS_SEF"] = array(
			"PARENT" => "CATEGORY_".$i["KEY"],
			"NAME" => GetMessage("CP_BMS_IS_SEF"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		);
		if($arCurrentValues["CATEGORY_".$i["KEY"]."_IS_SEF"] == 'Y') {
			$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_LINK"] = array(
				"PARENT" => "CATEGORY_".$i["KEY"],
				"NAME" => GetMessage("CP_BMS_SEF_BASE_URL"),
				"TYPE" => "STRING",
			);
			$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_SECTION_LINK"] = CIBlockParameters::GetPathTemplateParam(
				"SECTION",
				"SECTION_PAGE_URL",
				GetMessage("CP_BMS_SECTION_PAGE_URL"),
				"#SECTION_CODE#/",
				"CATEGORY_".$i["KEY"]
			);
			$arComponentParameters["PARAMETERS"]["CATEGORY_".$i["KEY"]."_DETAIL_LINK"] = CIBlockParameters::GetPathTemplateParam(
				"DETAIL",
				"DETAIL_PAGE_URL",
				GetMessage("CP_BMS_DETAIL_PAGE_URL"),
				"#SECTION_CODE#/#ELEMENT_ID#/",
				"CATEGORY_".$i["KEY"]
			);
		}
	}
}
?>