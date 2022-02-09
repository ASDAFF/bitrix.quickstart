<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arProperty_X = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

	if($arr["PROPERTY_TYPE"]!="F")
	{
		if($arr["MULTIPLE"] == "Y")
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		elseif($arr["PROPERTY_TYPE"] == "L")
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arTemplateParameters = array(
	"PRODUCT_PROPS_VARIABLE" => array(
		"PARENT" => "URL_TEMPLATES",
		"NAME" => GetMessage("TP_BC_PRODUCT_PROPS_VARIABLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "prop",
	),
	"PRODUCT_PROPERTIES" => array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("TP_BC_PRODUCT_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_X,
	),
);
?>