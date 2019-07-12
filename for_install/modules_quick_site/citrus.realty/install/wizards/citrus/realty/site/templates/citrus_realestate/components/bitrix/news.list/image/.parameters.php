<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arProperty_File = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues['IBLOCK_ID']));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("F")))
	{
		$arProperty_File[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arTemplateParameters = array(
	"RESIZE_IMAGE_WIDTH" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"RESIZE_IMAGE_HEIGHT" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"COLORBOX_MAXWIDTH" => Array(
		"NAME" => GetMessage("PARAM_COLORBOX_MAXWIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "800",
	),
	"COLORBOX_MAXHEIGHT" => Array(
		"NAME" => GetMessage("PARAM_COLORBOX_MAXHEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "600",
	),
	"MORE_PHOTO_PROPERTY" => Array(
		"NAME" => GetMessage("PARAM_MORE_PHOTO_PROPERTY"),
		"TYPE" => "LIST",
		"VALUES" => $arProperty_File,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "",
	),
);

?>