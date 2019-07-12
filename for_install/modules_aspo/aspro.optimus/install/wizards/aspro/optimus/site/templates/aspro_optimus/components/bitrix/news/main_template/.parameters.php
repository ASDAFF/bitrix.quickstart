<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
Loader::includeModule('iblock');

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arProperty = array();
$arIblocksFilter  = array();
if((IntVal($arCurrentValues["CATALOG_IBLOCK_ID1"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID2"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID3"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID4"]) > 0))
{
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID1"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID1"];
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID2"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID2"];
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID3"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID3"];
}



//if ($arIblocksFilter)
//{
	$arIBlock = array();
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_CATALOG_TYPE"], "ACTIVE"=>"Y"));
	while($arr=$rsIBlock->Fetch())
		$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	
	foreach($arIblocksFilter as $key=>$value)
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
		while ($arr=$rsProp->Fetch())
		{
			if($arr["PROPERTY_TYPE"] != "F")
				$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
	$arProperty_LNS = $arProperty;
//}



$arTemplateParameters = array(
	"CATALOG_FILTER_NAME" => Array(
		"NAME" => GetMessage("FILTER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "arrProductsFilter",
	),
	"IS_VERTICAL" => array(
		"NAME" => GetMessage("IS_VERTICAL"),
		"PARENT" => "LIST_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"DISPLAY_DATE" => array(
		"NAME" => GetMessage("DISPLAY_DATE"),
		"PARENT" => "VISUAL",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	
	"SHOW_FAQ_BLOCK" => array(
		"NAME" => GetMessage("SHOW_FAQ_BLOCK"),
		"PARENT" => "VISUAL",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		//"REFRESH" => "Y",
	),
	"SHOW_SERVICES_BLOCK" => array(
		"NAME" => GetMessage("SHOW_SERVICES_BLOCK"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		//"REFRESH" => "Y",
	),
	"SHOW_BACK_LINK" => array(
		"NAME" => GetMessage("SHOW_BACK_LINK"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"GALLERY_PROPERTY" => array(
		"NAME" => GetMessage("GALLERY_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "MORE_PHOTO",
	),
	"SHOW_GALLERY" => array(
		"NAME" => GetMessage("SHOW_GALLERY"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"LINKED_PRODUCTS_PROPERTY" => array(
		"NAME" => GetMessage("LINKED_PRODUCTS_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "LINK"
	),
	"SHOW_LINKED_PRODUCTS" => array(
		"NAME" => GetMessage("SHOW_LINKED_PRODUCTS"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),
	"PRICE_PROPERTY" => array(
		"NAME" => GetMessage("PRICE_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "PRICE"
	),
	"SHOW_PRICE" => array(
		"NAME" => GetMessage("SHOW_PRICE"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),
	/*"PERIOD_PROPERTY" => array(
		"NAME" => GetMessage("PERIOD_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "PERIOD"
	),
	"SHOW_PERIOD" => array(
		"NAME" => GetMessage("SHOW_PERIOD"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),*/
	
);
/*if($arCurrentValues["SHOW_FAQ_BLOCK"]=="Y"){
	$arTemplateParameters["FAQ_FORM_ID"] = Array(
		"NAME" => GetMessage("FAQ_FORM_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "1",
		"PARENT" => "VISUAL",
	);
}*/
?>