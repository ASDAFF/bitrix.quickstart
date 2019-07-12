<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main;
global $DB;
global $USER;
global $APPLICATION;
global $CACHE_MANAGER;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;
	
$arParams["ELEMENT_ID"] = intval($arParams["~ELEMENT_ID"]);
if($arParams["ELEMENT_ID"] > 0 && $arParams["ELEMENT_ID"]."" != $arParams["~ELEMENT_ID"])
{
	ShowError(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = trim($arParams["ELEMENT_CODE"]);

$arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
if ('N' != $arParams['CACHE_GROUPS'])
	$arParams['CACHE_GROUPS'] = 'Y';

if($this->StartResultCache(false, array($arNavigation, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))){

	if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	if($arParams["ELEMENT_ID"] <= 0)
		$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
			$arParams["ELEMENT_ID"],
			$arParams["ELEMENT_CODE"],
			false,
			false,
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			)
		);
	
	if($arParams["ELEMENT_ID"] > 0)
	{
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
			"LIST_PAGE_URL",
			"DETAIL_PICTURE",
			"PREVIEW_PICTURE",
			"PROPERTY_*",
		);
		
		$arFilter = array(
			"ID" => $arParams["ELEMENT_ID"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => 'R',
			"SHOW_HISTORY" => $WF_SHOW_HISTORY,
		);

		$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		$rsElement->SetUrlTemplates();
		$rsElement->SetSectionContext($arSection);
		
		if($obElement = $rsElement->GetNextElement())
		{
		
			$arResult = $obElement->GetFields();
			
			$arResult["CHAIN_EL"]["URL"] = $arResult['DETAIL_PAGE_URL'];
			$arResult["CHAIN_EL"]["NAME"] = $arResult['NAME'];
			
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);
			$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
			
			$arResult["PREVIEW_PICTURE"] = (0 < $arResult["PREVIEW_PICTURE"] ? CFile::GetFileArray($arResult["PREVIEW_PICTURE"]) : false);
			if ($arResult["PREVIEW_PICTURE"])
			{
				$arResult["PREVIEW_PICTURE"]["ALT"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
				if ($arResult["PREVIEW_PICTURE"]["ALT"] == "")
					$arResult["PREVIEW_PICTURE"]["ALT"] = $arResult["NAME"];
				$arResult["PREVIEW_PICTURE"]["TITLE"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
				if ($arResult["PREVIEW_PICTURE"]["TITLE"] == "")
					$arResult["PREVIEW_PICTURE"]["TITLE"] = $arResult["NAME"];
			}
			$arResult["DETAIL_PICTURE"] = (0 < $arResult["DETAIL_PICTURE"] ? CFile::GetFileArray($arResult["DETAIL_PICTURE"]) : false);
			if ($arResult["DETAIL_PICTURE"])
			{
				$arResult["DETAIL_PICTURE"]["ALT"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
				if ($arResult["DETAIL_PICTURE"]["ALT"] == "")
					$arResult["DETAIL_PICTURE"]["ALT"] = $arResult["NAME"];
				$arResult["DETAIL_PICTURE"]["TITLE"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
				if ($arResult["DETAIL_PICTURE"]["TITLE"] == "")
					$arResult["DETAIL_PICTURE"]["TITLE"] = $arResult["NAME"];
			}

			$arResult["PROPERTIES"] = $obElement->GetProperties();

			$arResult["DISPLAY_PROPERTIES"] = array();
			foreach($arParams["PROPERTY_CODE"] as $pid)
			{
				if (!isset($arResult["PROPERTIES"][$pid]))
					continue;
				$prop = &$arResult["PROPERTIES"][$pid];
				$boolArr = is_array($prop["VALUE"]);
				if(
					($boolArr && !empty($prop["VALUE"]))
					|| (!$boolArr && strlen($prop["VALUE"])>0)
				)
				{
					$arResult["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arResult, $prop, "catalog_out");
				}
			}
			
			$arButtons = CIBlock::GetPanelButtons(
				$arResult["IBLOCK_ID"],
				$arResult["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arResult["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arResult["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			
			//получаем типы цен для групп текущего пользователя
			$arGroups = $USER->GetUserGroupArray();
			
			if(is_array($arGroups)){
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup($arGroups,SITE_ID);
			}else{
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup();
			}
			
			//типы цен из настроек компонента
			if(is_array($arParams["PRICE"])){
				$newArPrice = array();
				foreach($priceTip as $key=>$p_id){
					if(in_array($p_id,$arParams["PRICE"])) $newArPrice[] = $p_id;
				}
				$priceTip = $newArPrice;
			}
			
			//получаем цены
			$arResult["PRICE"] = \Mlife\Asz\CurencyFunc::getPriceBase($priceTip,array($arResult["ID"]),SITE_ID);
			
			$arPrepareDiscount = array();
			foreach($arResult["PRICE"] as $elId=>$price){
				if($price["VALUE"]>0){
					$arPrepareDiscount[$elId] = $price["VALUE"];
				}
			}
			$arResult['DISCOUNT'] = \Mlife\Asz\PriceDiscount::getDiscountProducts($arPrepareDiscount,$arParams["IBLOCK_ID"],$arGroups,SITE_ID);
			
			//получаем остатки
			$res = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$arResult["ID"])));
			$arResult["QUANT"] = 0;
			if($arRes = $res->Fetch()){
				$arResult["QUANT"] = $arRes["KOL"];
			}
			
			//получаем список категорий для крошек
			$db_old_groups = CIBlockElement::GetElementGroups($arResult["ID"], false);
			$ar_new_group = false;
			$deph = 0;
			while($ar_group = $db_old_groups->GetNext()){
				if($ar_group["DEPTH_LEVEL"]>$deph){
					$ar_new_group = $ar_group["ID"];
					$deph = $ar_group["DEPTH_LEVEL"];
				}
			}
			$arResult["SECTIONS"] = array();
			if($ar_new_group){
				$nav = CIBlockSection::GetNavChain(false,$ar_new_group);
				while($arSectionPath = $nav->GetNext()){
					$arResult["SECTIONS"][] = $arSectionPath;
				}
			}
			
			
			
		}
		else
		{
			$this->AbortResultCache();
			ShowError(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
			@define("ERROR_404", "Y");
			if($arParams["SET_STATUS_404"]==="Y")
				CHTTP::SetStatus("404 Not Found");
				return;
		}
		
	}
	
	$this->SetResultCacheKeys(array(
		"CHAIN_EL",
		"IPROPERTY_VALUES",
		"ID",
		"SECTIONS"
		));
		
	$this->IncludeComponentTemplate();

}
if($arParams["SET_TITLE"]=="Y"){
	$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
	$APPLICATION->SetPageProperty("keywords", $arResult['IPROPERTY_VALUES']["ELEMENT_META_KEYWORDS"]);
	$APPLICATION->SetPageProperty("description", $arResult['IPROPERTY_VALUES']["ELEMENT_META_DESCRIPTION"]);
}


?>