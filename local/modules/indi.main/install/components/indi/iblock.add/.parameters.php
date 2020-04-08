<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if($arCurrentValues["IBLOCK_ID"] > 0)
{
	$arIBlock = CIBlock::GetArrayByID($arCurrentValues["IBLOCK_ID"]);

	$bWorkflowIncluded = ($arIBlock["WORKFLOW"] == "Y") && CModule::IncludeModule("workflow");
	$bBizproc = ($arIBlock["BIZPROC"] == "Y") && CModule::IncludeModule("bizproc");
}
else
{
	$bWorkflowIncluded = CModule::IncludeModule("workflow");
	$bBizproc = false;
}	
$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}	
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arProperty_LNSF = array(
	"NAME" => GetMessage("IBLOCK_ADD_NAME"),
	"TAGS" => GetMessage("IBLOCK_ADD_TAGS"),
	"DATE_ACTIVE_FROM" => GetMessage("IBLOCK_ADD_ACTIVE_FROM"),
	"DATE_ACTIVE_TO" => GetMessage("IBLOCK_ADD_ACTIVE_TO"),
	"IBLOCK_SECTION" => GetMessage("IBLOCK_ADD_IBLOCK_SECTION"),
	"PREVIEW_TEXT" => GetMessage("IBLOCK_ADD_PREVIEW_TEXT"),
	"PREVIEW_PICTURE" => GetMessage("IBLOCK_ADD_PREVIEW_PICTURE"),
	"DETAIL_TEXT" => GetMessage("IBLOCK_ADD_DETAIL_TEXT"),
	"DETAIL_PICTURE" => GetMessage("IBLOCK_ADD_DETAIL_PICTURE"),
);


$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "F")))
	{
		$arProperty_LNSF[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}
$arVirtualProperties = $arProperty_LNSF;
$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"IBLOCK_TYPE" => array(
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),

		"IBLOCK_ID" => array(
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"FIELDS" => array(
			"NAME" => GetMessage("FIELDS"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_LNSF,
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
		"REQUARED_FIELDS" => array(
			"NAME" => GetMessage("REQUARED_FIELDS"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_LNSF,
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
		"NAME_FORMAT" => array(
			"NAME" => GetMessage("NAME_FORMAT"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_LNSF,
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
		"USE_DATE_IN_NAME" => array(
			"NAME" => GetMessage("USE_DATE_IN_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ACTIVATE_ELEMENT" => array(
			"NAME" => GetMessage("ACTIVATE_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ERROR_MESSAGE" => array(
			"NAME" => GetMessage("ERROR_MESSAGE"),
			"TYPE" => "STRING"
		), 
		"SUCCESS_MESSAGE" => array(
			"NAME" => GetMessage("SUCCESS_MESSAGE"),
			"TYPE" => "STRING",
		),     
	),

);
foreach ($arVirtualProperties as $key => $title)
{
	$arComponentParameters["PARAMETERS"]["CUSTOM_LABELS_".$key] = array(
		"PARENT" => "TITLES",
		"NAME" => $title,
		"TYPE" => "STRING",
	);
}
?>
