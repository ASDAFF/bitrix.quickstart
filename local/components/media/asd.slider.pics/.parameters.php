<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock")) return;

$arTypesEx_IBLOCK_TYPE = array("-" => " ");
$rsIBlockTypes_IBLOCK_TYPE = CIBlockType::GetList(array("SORT" => "ASC"));
while($arIBlockTypes_IBLOCK_TYPE = $rsIBlockTypes_IBLOCK_TYPE->Fetch())
	if ($arIBType_IBLOCK_TYPE = CIBlockType::GetByIDLang($arIBlockTypes_IBLOCK_TYPE["ID"], LANG))
		$arTypesEx_IBLOCK_TYPE[$arIBlockTypes_IBLOCK_TYPE["ID"]] = $arIBType_IBLOCK_TYPE["NAME"];

$arIBlocks_IBLOCK_ID = array();
$arFilter = array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : ""));
$rsIBlock_IBLOCK_ID = CIBlock::GetList(array("SORT" =>" ASC"), $arFilter);
while($arIBlock_IBLOCK_ID = $rsIBlock_IBLOCK_ID->Fetch())
	$arIBlocks_IBLOCK_ID[$arIBlock_IBLOCK_ID["ID"]] = $arIBlock_IBLOCK_ID["NAME"];


$arComponentParameters = array(
	"GROUPS" => array(
		"DATA_SORT" => array(
			"NAME" => GetMessage("ASD_CMP_PARAM_SORT_BLOCK"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx_IBLOCK_TYPE,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks_IBLOCK_ID,
		),
		"PIC_FROM" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PIC_FROM"),
			"TYPE" => "LIST",
			"VALUES" => array("PREVIEW_PICTURE" => GetMessage("ASD_CMP_PARAM_PIC_FROM_PREV"), "DETAIL_PICTURE" => GetMessage("ASD_CMP_PARAM_PIC_FROM_DET")),
		),
		"SORT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_SORT"),
			"TYPE" => "LIST",
			"VALUES" => array("RAND" => GetMessage("ASD_CMP_PARAM_SORT_RAND"), "ASC" => GetMessage("ASD_CMP_PARAM_SORT_ASC")),
		),
		"LINK" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_LINK"),
			"TYPE" => "STRING",
			"VALUES" => "",
		),
		"COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_COUNT"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"COLS" => "5"
		),
		"TIME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_TIME"),
			"TYPE" => "STRING",
			"VALUES" => "",
			"COLS" => "5"
		),
		'CACHE_TIME' => array('DEFAULT' => 8640000)
	),
);
?>