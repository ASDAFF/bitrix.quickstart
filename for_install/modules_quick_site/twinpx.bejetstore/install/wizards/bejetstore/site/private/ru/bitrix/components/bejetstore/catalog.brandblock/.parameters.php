<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule("iblock"))
	return;

if(!Loader::includeModule("highloadblock"))
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
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
		"PROP_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_PROP_CODE"),
			"TYPE" => "LIST",
			"PARENT" => "BASE",
			"VALUES" => $arProps,
		),
		"WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_CB_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "120"
		),
		"HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_CB_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "50"
		),
		"WIDTH_SMALL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_CB_WIDTH_SMALL"),
			"TYPE" => "STRING",
			"DEFAULT" => "21"
		),
		"HEIGHT_SMALL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_CB_HEIGHT_SMALL"),
			"TYPE" => "STRING",
			"DEFAULT" => "17"
		),
		"CACHE_TIME"  =>  array(
			"DEFAULT" => 36000000
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("IBLOCK_CB_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		)
	)
);
?>