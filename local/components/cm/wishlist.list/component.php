<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

$arUrlTemplates = array(
	"list" => "#SECTION_CODE#/",
);
$arVariables = array();
$page = 
    CComponentEngine::ParseComponentPath("/wishlist/", 
                                         $arUrlTemplates, $arVariables);
										 
if($arParams["IBLOCK_ID"] < 1) {
	ShowError("IBLOCK_ID IS NOT DEFINED");
	return false;
}

if(!isset($arParams["ITEMS_LIMIT"])) {
	$arParams["ITEMS_LIMIT"] = 10;
}

$arNavParams = array();

if ($arParams["ITEMS_LIMIT"] > 0) {
	$arNavParams = array(
		"nPageSize" => $arParams["ITEMS_LIMIT"],
	);
}

$arNavigation = CDBResult::GetNavParams($arNavParams);
GLOBAL $USER;
$user_id = $USER->GetId();

if($this->StartResultCache(false, array($arNavigation, $arVariables, $user_id)))
{
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError("IBLOCK_MODULE_NOT_INSTALLED");
		return false;
	}
	if($user_id){
		$arSort= array("SORT" => "ASC", "DATE_ACTIVE_FROM" => "DESC", "ID" => "DESC");
		$arFilter = array("IBLOCK_ID" => 2, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "CREATED_BY" => $user_id);
		$arSelect = array("ID", "NAME", "IBLOCK_SECTION_ID", "PROPERTY_WISH");
		if($arVariables['SECTION_CODE']){
			$arFilter['SECTION_CODE'] = $arVariables['SECTION_CODE'];
		}
		$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
		if ($arParams["DETAIL_URL"]) {
			$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
		}
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$res = CIBlockSection::GetByID($arElement["IBLOCK_SECTION_ID"]);
			if($ar_res = $res->GetNext())
				$arElement['IBLOCK_SECTION_NAME'] = $ar_res['NAME'];
			$arSortEl= array("SORT" => "ASC", "DATE_ACTIVE_FROM" => "DESC", "ID" => "DESC");
			$arFilterEl = array("IBLOCK_ID" => 1, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "ID" => $arElement['PROPERTY_WISH_VALUE']);
			$arSelectEl = array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_article", "PROPERTY_model", "PROPERTY_type");
			$rsElementEl = CIBlockElement::GetList($arSortEl, $arFilterEl, false, $arNavParams, $arSelectEl);
			while($obElementEl = $rsElementEl->GetNextElement()) {
				$arElementEl = $obElementEl->GetFields();
				if ($arElementEl["PREVIEW_PICTURE"]) {
					$arElementEl["PREVIEW_PICTURE"] = CFile::GetFileArray($arElementEl["PREVIEW_PICTURE"]);
				} 
				$arPrice = CPrice::GetBasePrice($arElementEl["ID"]);
				$arElementEl["PRICE"] = $arPrice["PRICE"];
				$arElementEl["PROPERTIES"]["article"]["VALUE"] = $arElementEl["PROPERTY_ARTICLE_VALUE"];
				$arElementEl["PROPERTIES"]["article"]["ID"] = $arElementEl["PROPERTY_ARTICLE_VALUE_ID"];
				$arElementEl["PROPERTIES"]["model"]["VALUE"] = $arElementEl["PROPERTY_MODEL_VALUE"];
				$arElementEl["PROPERTIES"]["model"]["ID"] = $arElementEl["PROPERTY_MODEL_VALUE_ID"];
				$arElementEl["PROPERTIES"]["type"]["VALUE"] = $arElementEl["PROPERTY_TYPE_VALUE"];
				$arElementEl["PROPERTIES"]["type"]["ID"] = $arElementEl["PROPERTY_TYPE_VALUE_ID"];
				$arElementEl["PROPERTIES"]["type"]["ENUM_ID"] = $arElementEl["PROPERTY_TYPE_ENUM_ID"];
				unset(	$arElementEl["~ID"],
						$arElementEl["~NAME"],
						$arElementEl["~PREVIEW_PICTURE"],
						$arElementEl["~PROPERTY_ARTICLE_VALUE"],
						$arElementEl["~PROPERTY_ARTICLE_VALUE_ID"],
						$arElementEl["~PROPERTY_MODEL_VALUE"],
						$arElementEl["~PROPERTY_MODEL_VALUE_ID"],
						$arElementEl["~PROPERTY_TYPE_VALUE"],
						$arElementEl["~PROPERTY_TYPE_ENUM_ID"],
						$arElementEl["~PROPERTY_TYPE_VALUE_ID"],
						$arElementEl["~SORT"],
						$arElementEl["~ACTIVE_FROM"],
						$arElementEl["ACTIVE_FROM"],
						$arElementEl["PROPERTY_ARTICLE_VALUE"],
						$arElementEl["PROPERTY_ARTICLE_VALUE_ID"],
						$arElementEl["PROPERTY_MODEL_VALUE"],
						$arElementEl["PROPERTY_MODEL_VALUE_ID"],
						$arElementEl["PROPERTY_TYPE_VALUE"],
						$arElementEl["PROPERTY_TYPE_VALUE_ID"],
						$arElementEl["PROPERTY_TYPE_ENUM_ID"]
					);
				$arResult["SECTION"]['ID'] = $arElement['IBLOCK_SECTION_ID'];
				$arResult["SECTION"]['NAME'] = $arElement['IBLOCK_SECTION_NAME'];
				$arResult["ITEMS"][] = $arElementEl;
			}
		}
		$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, "Страницы", "", "");
		$this->SetResultCacheKeys(array(
			"ID",
			"IBLOCK_ID",
			"NAV_CACHED_DATA",
			"NAME",
			"IBLOCK_SECTION_ID",
			"IBLOCK",
			"LIST_PAGE_URL", 
			"~LIST_PAGE_URL",
			"SECTION",
			"PROPERTIES",
		));
		$this->IncludeComponentTemplate();
		if (empty($arResult)) {
			$this->AbortResultCache();
			ShowError("404 Not Found");
			@define("ERROR_404", "Y");
			CHTTP::SetStatus("404 Not Found");
		}	
	}
}

?>