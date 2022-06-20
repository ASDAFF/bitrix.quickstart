<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(empty($arParams["FILTER_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

\Bitrix\Main\Loader::includeModule("iblock");

$arSort = array(
		$arParams["SORT_BY1"] => $arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"] => $arParams["SORT_ORDER2"],
	);

if ($arParams['RAND_ELEMENTS'] == 'Y') 
	$arSort  = array(
		"RAND" => "ASC"
	);

$arFilter = array(
		"IBLOCK_ID"			 => $arParams["IBLOCK_ID"],
		"IBLOCK_LID"		 => SITE_ID,
		"IBLOCK_ACTIVE"		 => "Y",
		//"ACTIVE_DATE"		 => "Y",
		"ACTIVE"			 => "Y",
		"CHECK_PERMISSIONS"	 => "Y",
		"MIN_PERMISSION"	 => "R",
	);

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
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"DATE_CREATE",
		"CREATED_BY",
		"TIMESTAMP_X",
		"MODIFIED_BY",
		"TAGS",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_PICTURE",
		"PREVIEW_PICTURE",
		"PROPERTY_*"
	);

$arNavParams = array(
	"nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
);

$rsElements = CIBlockElement::GetList($arSort, array_merge($arrFilter, $arFilter), false, $arNavParams, $arSelect);
$arResult['ITEMS'] = array();
$i = 0;
while($ob = $rsElements->GetNextElement())
{
	$arItem					= $ob->GetFields();
	$arItem['PROPERTIES']	= $ob->GetProperties();
	
	$arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
	$arItem["DETAIL_PICTURE"]  = (0 < $arItem["DETAIL_PICTURE"]  ? CFile::GetFileArray($arItem["DETAIL_PICTURE"])  : false);
	
	$arButtons = CIBlock::GetPanelButtons(
		$arItem["IBLOCK_ID"],
		$arItem["ID"],
		0,
		array("SECTION_BUTTONS"=>false, "SESSID"=>false)
	);
	$arItem["EDIT_LINK"]   = $arButtons["edit"]["edit_element"]["ACTION_URL"];
	$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
	
	$arResult['ITEMS'][$i] = $arItem;
	$i++;
}

$this->IncludeComponentTemplate();
?>
