<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!CModule::IncludeModule("iblock"))
	return;

if (!CModule::IncludeModule("sale"))
	return;

if (!CModule::IncludeModule("catalog"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(

	),


	"PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
        "ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_CODE"]}',
		),
        "TIME_ZADERZH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_TIME_ZADERZH"),
			"TYPE" => "STRING",
            "DEFAULT" => 1,
		),
        "TIME_SHOW" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_TIME_SHOW"),
			"TYPE" => "STRING",
            "DEFAULT" => 5,
		),
        "COLOR" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_COLOR"),
			"TYPE" => "STRING",
            "DEFAULT" => "#7C8695",
		),
        "JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHS_JQUERY"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
		),
	),
);
?>