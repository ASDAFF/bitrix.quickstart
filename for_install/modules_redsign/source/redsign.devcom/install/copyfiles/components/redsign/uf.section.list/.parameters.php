<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

$arIblocks = array();
$res = CIBlock::GetList(array(), array(), true);
while($ar_res = $res->Fetch())
{
	$arIblocks[$ar_res["ID"]] = "[".$ar_res["CODE"]."] ".$ar_res["NAME"];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arIblocks,
			"REFRESH" => "Y",
			"PARENT" => "BASE",
		),
		"COUNT" => array(
			"NAME" => GetMessage("COUNT"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "3",
		),
		"UF_CODE" => array(
			"NAME" => GetMessage("UF_CODE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"UF_VALUE" => array(
			"NAME" => GetMessage("UF_VALUE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"UF_VALUE_NOT" => array(
			"NAME" => GetMessage("UF_VALUE_NOT"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => "BASE",
		),
		"MAX_WIDTH" => array(
			"NAME" => GetMessage("MAX_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "180",
		),
		"MAX_HEIGHT" => array(
			"NAME" => GetMessage("MAX_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "180",
		),
		"CACHE_TIME"  =>  Array(
			"PARENT" => "CACHE_SETTINGS",
			"DEFAULT" => 3600
		),
	),
);
?>
