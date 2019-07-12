<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];	
}

$arTemplateParameters = array(
	"DISPLAY_AS_RATING" => Array(
		"NAME" => GetMessage("TP_CBIV_DISPLAY_AS_RATING"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"rating" => GetMessage("TP_CBIV_RATING"),
			"vote_avg" => GetMessage("TP_CBIV_AVERAGE"),
		),
		"DEFAULT" => "rating",
	),
	 "ADD_PRODUSER_TO_TITLE" => Array(
		"NAME" => GetMessage("ADD_PRODUSER_TO_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_FRACTION_PRICE" => Array(
		"NAME" => GetMessage("SHOW_FRACTION_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"PROPANALOG" => Array(
		"NAME" => GetMessage("PROPANALOG"),
		"TYPE" => "LIST",
		"MULTIPLE"=>"Y",
		"DEFAULT" => "",
		"VALUES" => $arProperty_LNS,
	),
	"PERCENT_FOR_NUM" => Array(
		"NAME" => GetMessage("PERCENT_FOR_NUM"),
		"TYPE" => "STRING",		
		"DEFAULT" => "50",
	),
	"PERCENT_FOR_PROPERTY" => Array(
		"NAME" => GetMessage("PERCENT_FOR_PROPERTY"),
		"TYPE" => "STRING",		
		"DEFAULT" => "50",
	)
);
?>