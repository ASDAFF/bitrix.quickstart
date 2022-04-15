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
ESGroupProperty::SetDefault($arParams, "CACHE_TIME", 3600);

if($this->StartResultCache(false, $USER->GetGroups()))
{
	$arProperties = array();
	$rsProperties = CIBlockElement::GetProperty($arParams["ES_IBLOCK_CATALOG"], intval($arParams["ES_ELEMENT"]));
	while($p=$rsProperties->Fetch()) if($p["CODE"] != "") $arProperties[$p["CODE"]] = $p;

	$arResult = array();
	for($i = 0; $i < intval($arParams["ES_GROUP_COUNT"]); $i++)
	{
		$arItem = array();
		$arProp = array();
		foreach($arParams["ES_GROUP_".$i] as $p)
		{
			$arProp[$p] = CIBlockFormatProperties::GetDisplayValue($arItem, $arProperties[$p], "catalog_out");
			if($arParams["ES_REMOVE_HREF"] == "Y" && preg_match("/<(.+)>(.+)<\/a>/i", $arProp[$p]["DISPLAY_VALUE"], $arValue) == 1) $arProp[$p]["DISPLAY_VALUE"] = $arValue[2];
		}
		
		$arResult[] = array(
			"ID" => $i,
			"NAME" => $arParams["ES_GROUP_NAME_".$i],
			"PROPERTIES" => $arProp,
		);
	}
	$this->IncludeComponentTemplate();
}
?>