<?
######################################################
# Name: energosoft.slider                            #
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
if(!CModule::IncludeModule("energosoft.slider")) return;
include_once($_SERVER["DOCUMENT_ROOT"].$this->GetPath()."/tools.php");

if(!is_array($arParams["ES_PROPERTY"])) $arParams["ES_PROPERTY"] = array();
foreach($arParams["ES_PROPERTY"] as $k=>$v) if($v==="") unset($arParams["ES_PROPERTY"][$k]);

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ES_SECTION_ID"] = intval($arParams["ES_SECTION_ID"]);
$arParams["ES_BLOCK_WITDH"] = intval($arParams["ES_BLOCK_WITDH"]);
$arParams["ES_BLOCK_HEIGHT"] = intval($arParams["ES_BLOCK_HEIGHT"]);
$arParams["ES_BLOCK_MARGIN"] = intval($arParams["ES_BLOCK_MARGIN"]);
$arParams["ES_STEP"] = intval($arParams["ES_STEP"]);
$arParams["ES_COUNT"] = intval($arParams["ES_COUNT"]);
$arParams["ES_AUTO"] = intval($arParams["ES_AUTO"]);
$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
if($arParams["CACHE_TIME"]=="") $arParams["CACHE_TIME"]=3600;

if($this->StartResultCache(false, $USER->GetGroups()))
{
	$arSelect = array(
		"ID",
		"NAME",
		"CODE",
		"IBLOCK_ID",
		"IBLOCK_CODE",
		"IBLOCK_SECTION_ID",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
		"LIST_PAGE_URL",
		"DETAIL_PAGE_URL",
	);

	$arSort = array($arParams["ES_SORT_FIELD"] => $arParams["ES_SORT_ORDER"]);
	if($arParams["ES_SORT_ORDER"] == "rand") $arSort = array("rand"=>"rand");

	$arFilter = array(
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE" => "Y",
	);
	if($arParams["ES_SECTION_ID"]!="") $arFilter["SECTION_ID"] = $arParams["ES_SECTION_ID"];

	$arResult = array();
	$arResult["ID"] = uniqid("_");
	$arResult["ITEMS"] = array();

	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	$rsElements->SetUrlTemplates($arParams["ES_DETAIL_URL"]);
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = array();
		$arItem = $obElement->GetFields();

		$arProperties = array();
		$rsProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arItem["ID"]);
		while($p = $rsProperties->Fetch()) $arProperties[$p["CODE"]] = $p;
		if($arParams["ES_URL_TYPE"] == "property") if(isset($arProperties[$arParams["ES_PROPERTY_URL"]])) if($arProperties[$arParams["ES_PROPERTY_URL"]]["VALUE"] != "") $arItem["URL"] = $arProperties[$arParams["ES_PROPERTY_URL"]]["VALUE"];
		if($arParams["ES_URL_TYPE"] == "iblock") $arItem["URL"] = $arItem["DETAIL_PAGE_URL"];

		$arItem["DISPLAY_PROPERTIES"] = array();
		foreach($arParams["ES_PROPERTY"] as $prop)
		{
			$arItem["DISPLAY_PROPERTIES"][$prop] = array(
				"NAME"=>$arProperties[$prop]["NAME"],
				"VALUE"=>$arProperties[$prop]["VALUE"],
			);
		}

		$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
		$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
		$arResult["ITEMS"][] = $arItem;
	}

	$this->InitComponentTemplate();
	$template = &$this->GetTemplate();

	$arResult["ES_HASH"] = "-".ESSlider::ES_GetHash($arParams["ES_SHOW_BUTTONS"],$arParams["ES_BLOCK_WITDH"],$arParams["ES_BLOCK_HEIGHT"],$arParams["ES_BLOCK_MARGIN"],$arParams["ES_COUNT"]);
	$arResult["ES_TEMPLATE"] = $template->GetFolder();
	$this->ShowComponentTemplate();
}
ESSlider::ES_GenerateCSS($arResult["ES_HASH"],$this->GetPath(),$arResult["ES_TEMPLATE"],$arParams["ES_ORIENTATION"],$arParams["ES_SHOW_BUTTONS"],$arParams["ES_BLOCK_WITDH"],$arParams["ES_BLOCK_HEIGHT"],$arParams["ES_BLOCK_MARGIN"],$arParams["ES_COUNT"]);

$APPLICATION->SetAdditionalCSS($arResult["ES_TEMPLATE"]."/template".$arResult["ES_HASH"].".css");
if($arParams["ES_INCLUDE_JQUERY"]=="Y") $APPLICATION->AddHeadScript("/bitrix/js/energosoft/jquery-1.6.4.min.js");
if($arParams["ES_INCLUDE_JQUERY_EASING"]=="Y") $APPLICATION->AddHeadScript("/bitrix/js/energosoft/jquery.animation.easing.js");
if($arParams["ES_INCLUDE_JQUERY_JCAROUSEL"]=="Y") $APPLICATION->AddHeadScript("/bitrix/js/energosoft/jquery.jcarousel.js");
?>