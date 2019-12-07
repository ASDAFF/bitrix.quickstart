<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arFilter = array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE"=>"Y",
		"ACTIVE"=>"Y",
		"GLOBAL_ACTIVE"=>"Y",
		"!PROPERTY_TOP"=>false,
	);
	
	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"CODE",
		"XML_ID",
		"NAME",
		"ACTIVE",
		"DATE_ACTIVE_FROM",
		"DATE_ACTIVE_TO",
		"SORT",
		"DATE_CREATE",
		"CREATED_BY",
		"TIMESTAMP_X",
		"MODIFIED_BY",
		"TAGS",
		"IBLOCK_SECTION_ID",
	);	
	
	//ORDER BY
	$arSort = array(
		"sort"=>"asc",
	);
	
    $arResult['~SECTIONS'] = array();
	
	//EXECUTE
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	while($arItem = $rsElements->GetNext())
		$arResult['~SECTIONS'][$arItem["IBLOCK_SECTION_ID"]]["ELEMENT_CNT"]++;
?>