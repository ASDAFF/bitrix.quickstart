<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arProperty = array(
    "IBLOCK_SECTION_ID" => GetMessage("TABC_IBLOCK_SECTION"),
    "IBLOCK_ELEMENT_NAME" => GetMessage("TABC_IBLOCK_ELEMENT"),
);
if (0 < intval($arCurrentValues["IBLOCK_ID"]))
{
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}



$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TABC_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TABC_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"PROPERTY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TABC_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty,
			"DEFAULT" => "IBLOCK_SECTION_ID",
		),
        "REQUEST_KEY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("TABC_IBLOCK_REQUEST_KEY"),
            "TYPE" => "STRING",
            "DEFAULT" => "ID",
        ),
        "FILTER_NAME" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("TABC_IBLOCK_FILTER"),
            "TYPE" => "STRING",
            "DEFAULT" => "arrFilter",
        ),

		"CACHE_TIME" => array(
            "DEFAULT" => 86400
        ),
	),
);

?>