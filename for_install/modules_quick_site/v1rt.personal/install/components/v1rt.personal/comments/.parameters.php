<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["ID_IBLOCK"]));
while($prop_fields = $properties->GetNext())
    $arProp[$prop_fields["CODE"]] = "[".$prop_fields["ID"]."] ".$prop_fields["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"ID_IBLOCK" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ID_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
        "PROPERTY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("PROPERTY"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProp,
			"REFRESH" => "Y",
		),
		"ID_RECORD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ID_RECORD"),
			"TYPE" => "TEXTBOX",
		),
        "NO_USE_CAPTCHA" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("NO_USE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
		),
	),
);
?>