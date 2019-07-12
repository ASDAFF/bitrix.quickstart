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
	"IBLOCK_CATALOG_TYPE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CATALOG_TYPE"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID1" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK1"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID2" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK2"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID3" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK3"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlock,
		"ADDITIONAL_VALUES" => "Y",
		"REFRESH" => "Y",
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
		"DEFAULT" => "BRAND"
	),
	"SHOW_LINKED_PRODUCTS" => array(
		"NAME" => GetMessage("SHOW_LINKED_PRODUCTS"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),
);
?>