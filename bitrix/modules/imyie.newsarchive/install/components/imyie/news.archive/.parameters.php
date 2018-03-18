<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule('iblock');

$arIblockTypes = CIBlockParameters::GetIBlockTypes(Array("" => GetMessage("IMYIE_ARDATE_VARIANT_NONE")));

$arIBlocks = array();
$arIBlocks[] = GetMessage("IMYIE_ARDATE_VARIANT_NONE");
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = '['.$arRes["ID"].'] '.$arRes["NAME"];

$arDate = array(
	"" => GetMessage("IMYIE_ARDATE_VARIANT_NONE"),
	"TIMESTAMP_X" => GetMessage("IMYIE_ARDATE_VARIANT1"),
	"DATE_ACTIVE_FROM" => GetMessage("IMYIE_ARDATE_VARIANT2"),
	"DATE_ACTIVE_TO" => GetMessage("IMYIE_ARDATE_VARIANT3"),
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"NAME" => GetMessage("IMYIE_IBTYPE_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arIblockTypes,
			"MULTIPLE" => "N",
			"PARENT" => "BASE",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"NAME" => GetMessage("IMYIE_IBLOCK_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"MULTIPLE" => "N",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"ORDERT_VARIANT" => array(
			"NAME" => GetMessage("IMYIE_ARDATE_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arDate,
			"MULTIPLE" => "N",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"KNOW_CNT_ELEMENTS" => array(
			"NAME" => GetMessage("IMYIE_KNOW_CNT_ELEMENTS"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"ONLY_ACTIVE_ELEMENTS" => array(
			"NAME" => GetMessage("IMYIE_ONLY_ACTIVE_ELEMENTS"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"CNT_MONTH" => array(
			"NAME" => GetMessage("IMYIE_LASTDATE_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"FILTER_LINK" => array(
			"NAME" => GetMessage("IMYIE_FILTER_LINK"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		),
		"CACHE_TIME"  => array(
			"DEFAULT" => 3600
		),
	),
);
?>