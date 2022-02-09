<?
######################################################
# Name: energosoft.grouping                          #
# File: component.php                                #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;
if(!CModule::IncludeModule("energosoft.grouping")) return;

ESGroupProperty::CheckIBlockID($arParams, "ES_IBLOCK_CATALOG");
ESGroupProperty::CheckIBlockID($arParams, "ES_IBLOCK_GROUP");
ESGroupProperty::SetDefault($arParams, "CACHE_TIME", 3600);

if($this->StartResultCache(false, $USER->GetGroups()))
{
	$arProperties = array();
	$rsProperties = CIBlockElement::GetProperty($arParams["ES_IBLOCK_CATALOG"], intval($arParams["ES_ELEMENT"]));
	while($p=$rsProperties->Fetch()) if($p["CODE"] != "") $arProperties[$p["CODE"]] = $p;

	$arSelect = array(
		"ID",
		"NAME",
		"CODE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
	);
	$arSort = array($arParams["ES_IBLOCK_GROUP_SORT_FIELD"] => $arParams["ES_IBLOCK_GROUP_SORT_ORDER"]);
	$arFilter = array(
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["ES_IBLOCK_GROUP"],
		"IBLOCK_ACTIVE" => "Y",
	);

	$arResult = array();
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = array();
		$arItem = $obElement->GetFields();

		$arProp = array();
		foreach($arParams["ES_GROUP_".$arItem["ID"]] as $p)
		{
			$arProp[$p] = CIBlockFormatProperties::GetDisplayValue($arItem, $arProperties[$p], "catalog_out");
			if($arParams["ES_REMOVE_HREF"] == "Y" && preg_match("/<(.+)>(.+)<\/a>/i", $arProp[$p]["DISPLAY_VALUE"], $arValue) == 1) $arProp[$p]["DISPLAY_VALUE"] = $arValue[2];
		}
		
		$arResult[] = array(
			"ID" => $arItem["ID"],
			"NAME" => $arItem["NAME"],
			"CODE" => $arItem["CODE"],
			"PREVIEW_TEXT" => $arItem["PREVIEW_TEXT"],
			"PREVIEW_TEXT_TYPE" => $arItem["PREVIEW_TEXT_TYPE"],
			"DETAIL_TEXT" => $arItem["DETAIL_TEXT"],
			"DETAIL_TEXT_TYPE" => $arItem["DETAIL_TEXT_TYPE"],
			"PREVIEW_PICTURE" => CFile::GetFileArray($arItem["PREVIEW_PICTURE"]),
			"DETAIL_PICTURE" => CFile::GetFileArray($arItem["DETAIL_PICTURE"]),
			"PROPERTIES" => $arProp,
		);
	}
	$this->IncludeComponentTemplate();
}
?>