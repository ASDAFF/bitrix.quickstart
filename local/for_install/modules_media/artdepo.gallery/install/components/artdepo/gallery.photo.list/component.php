<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if(!$arParams["LANGUAGE_ID"])
    $arParams["LANGUAGE_ID"] = LANGUAGE_ID;
$name_lbl = "NAME_" . strtoupper($arParams["LANGUAGE_ID"]);

$arParams["PARENT_ID"] = intval($arParams["PARENT_ID"]);
if($arParams["PARENT_ID"]<=0){
	ShowError(GetMessage("PARENT_ID_NOT_DEFINED")); // TODO: Задать текст
	return;
}

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ID";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 1000;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]=="Y"; //Turn off by default
$arParams["ADD_ALBUMS_CHAIN"] = $arParams["ADD_ALBUMS_CHAIN"]=="Y"; //Turn off by default

$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

$bUSER_HAVE_ACCESS = true;

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED")); // TODO: Написать текст
		return;
	}
	if(!CModule::IncludeModule("artdepo.gallery"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("ARTDEPO_GALLERY_MODULE_NOT_INSTALLED")); // TODO: Написать текст
		return;
	}
	
	// Get Collection By ID
	$arResult = CArtDepoGallerySection::GetByID($arParams["PARENT_ID"]);
	if($arResult)
	{
	    $arResult["TOP_SECTION"] = CArtDepoGallerySection::GetByID($arResult["PARENT_ID"]);
	    
	    $arResult["NAME"] = ($arResult[$name_lbl]) ? $arResult[$name_lbl] : $arResult["NAME"];
	    $arResult["TOP_SECTION"]["NAME"] = ($arResult["TOP_SECTION"][$name_lbl]) ? $arResult["TOP_SECTION"][$name_lbl] : $arResult["TOP_SECTION"]["NAME"];
	
		//WHERE
		$arFilter = array (
			"PARENT_ID" => $arResult["ID"],
			"ACTIVE" => "Y",
		);

		//ORDER BY
		$arSort = array(
			$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		);

		$obParser = new CTextParser;
		$arResult["ITEMS"] = array();
		$arResult["ELEMENTS"] = array();
		$rsElement = CArtDepoGalleryImage::GetList($arSort, $arFilter);
		$rsElement->NavStart($arParams["NEWS_COUNT"]);
		while($arItem = $rsElement->GetNext())
		{
		    // Skip first item if it is an album cover, and user don't want to see it in photo list
		    if($arParams["SKIP_FIRST"] == "Y" && !$bSkipped){
		        $bSkipped = true;
		        continue;
	        }
		    $arItem["NAME"] = ($arItem[$name_lbl]) ? $arItem[$name_lbl] : $arItem["NAME"];
			if($arParams["NAME_TRUNCATE_LEN"] > 0)
				$arItem["NAME"] = $obParser->html_cut($name, $arItem["NAME"]);

    		if(strlen($arItem["DATE_UPDATE"])>0)
				$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["DATE_UPDATE"], "YYYY-MM-DD HH:MI:SS"));
			else
				$arItem["DISPLAY_ACTIVE_FROM"] = "";

			$arResult["ITEMS"][] = $arItem;
			$arResult["ELEMENTS"][] = $arItem["ID"];
		}
		$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
		$arResult["NAV_RESULT"] = $rsElement;
		$this->SetResultCacheKeys(array(
			"ID",
			"NAME",
			"TOP_SECTION",
			"NAV_CACHED_DATA",
			"ELEMENTS",
		));
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}

if(isset($arResult["ID"]))
{
	$arTitleOptions = null;

	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

	if($arParams["SET_TITLE"])
	{
		$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}
	
	// Include popup template
	if($arParams["POPUP_TEMPLATE"]){
	    $APPLICATION->AddHeadScript($this->GetPath()."/popup_templates/".$arParams["POPUP_TEMPLATE"]."/script.js");
    }

	return $arResult["ELEMENTS"];
}
?>
