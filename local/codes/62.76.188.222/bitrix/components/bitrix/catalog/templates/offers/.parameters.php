<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["LINK_IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arTemplateParameters = array(
	"OFFERS_FIELDS" => Array(
		"NAME" => GetMessage("TP_BC_OFFERS_FIELDS"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => array(
			"NAME" => GetMessage("IBLOCK_FIELD_NAME"),
		),
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "NAME",
	),
	"OFFERS_PROPERTIES" => Array(
		"NAME" => GetMessage("TP_BC_OFFERS_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	),
);
?>
