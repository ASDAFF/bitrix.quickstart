<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult = array();

//deb($arParams);

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if(!isset($arParams["CACHE_TYPE"]))
	$arParams["CACHE_TYPE"] = "Y";

if (!isset($arParams["IBLOCK_ID"]))
	return;

if (empty($arParams["ELEMENT_CODE"])) {
    $error404 = true;
}
$cacheParams = $USER->GetGroups();

if ($this->StartResultCache($arParams["CACHE_TIME"], $cacheParams)) {

	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
	}
	else {

		$arFilter = array(
            "IBLOCK_CODE" => "LandingPages", "ACTIVE" => "Y", "CODE" => $arParams["ELEMENT_CODE"]
        );
		$arSelect = array(
            'ID', 'NAME', "PREVIEW_TEXT", "DETAIL_TEXT",
            "PROPERTY_PRODUCT_ID", "PROPERTY_TITLE", "PROPERTY_DESCRIPTION", "PROPERTY_KEYWORDS",
        );


		$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);

		if ($data = $rsElement -> Fetch()) {

            $arResult["LANDING_ELEMENT"] = $data;

		} else {
            $error404 = true;
        }

        $this->SetResultCacheKeys(array(
            "LANDING_ELEMENT"
        ));

        if ($error404 == true) {
            @define("ERROR_404", "Y");
            @define("SEARCH_NOT_FOUND", "N");
            $arResult['SEARCH_NOT_FOUND'] = "N";
            $this -> IncludeComponentTemplate('notfound');
            return;

        } else {
		    $this -> IncludeComponentTemplate();
        }
	}	
}

Novagroup_Classes_General_Main::SetPageProperty("title", $arResult["LANDING_ELEMENT"]["PROPERTY_TITLE_VALUE"]);
Novagroup_Classes_General_Main::SetPageProperty("keywords", $arResult["LANDING_ELEMENT"]["PROPERTY_KEYWORDS_VALUE"]);
Novagroup_Classes_General_Main::SetPageProperty("description", $arResult["LANDING_ELEMENT"]["PROPERTY_DESCRIPTION_VALUE"]);
?>
