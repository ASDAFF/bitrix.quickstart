<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

if(!\Bitrix\Main\Loader::includeModule("highloadblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));

while($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProps = array();

$rsProps = CIBlockProperty::GetList(
	array("SORT" => "ASC", "ID" => "ASC"),
	array(
		"IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"PROPERTY_TYPE" => "S"
	)
);

while ($arProp = $rsProps->Fetch())
{
	if ($arProp['USER_TYPE'] !== 'directory')
		continue;
	if (isset($arProp['USER_TYPE_SETTINGS']) && isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
	{
		$arProps[$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
	}
}


$arComponentParameters = array(
	"GROUPS" => array(
		"MODERATION_SETTINGS" => array(
			"NAME" => GetMessage("EMARKET_COMMENT_MODERATION_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["code"]}',
		),
		"HLBLOCK_PROP_CODE" => array(
			"NAME" => GetMessage("HLBLOCK_PROP_CODE"),
			"TYPE" => "LIST",
			"PARENT" => "BASE",
			"VALUES" => $arProps,
		),
		"HLBLOCK_CR_PROP_CODE" => array(
			"NAME" => GetMessage("HLBLOCK_CR_PROP_CODE"),
			"TYPE" => "LIST",
			"PARENT" => "BASE",
			"VALUES" => $arProps,
		),
		"EMARKET_COMMENT_PREMODERATION" => array(
			"PARENT" => "MODERATION_SETTINGS",
			"NAME" => GetMessage("EMARKET_COMMENT_PREMODERATION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	)
);