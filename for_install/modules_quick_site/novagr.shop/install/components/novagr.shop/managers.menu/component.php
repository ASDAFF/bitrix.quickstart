<?php


if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if(!isset($arParams["CACHE_FOR_REQUEST_URI"]))
	$arParams["CACHE_FOR_REQUEST_URI"] = "N";
	

if ($arParams["COUNT_ELEMENTS"] != "Y")  {
	$arParams["COUNT_ELEMENTS"] = "N";
}


$arFilter = array(
	'CODE'			=> $arParams["IBLOCK_ELEMENT_CODE"],
	'IBLOCK_CODE'	=> $arParams["IBLOCK_CODE"],
);

// кэшируем в зависимости от урла
if ($arParams["CACHE_FOR_REQUEST_URI"] == "Y") {
	$cacheParams = $_SERVER["REQUEST_URI"];
} else {
	$cacheParams = false;
}

/**
 * поднимается вверх по дереву секций и проставляет количество товаров
 * @param array $arSection
 * @param array $arSECTIONS
 * @param int $currentCount
 */

function upCountParents($arSection, &$arSECTIONS, $currentCount=0) {
	
	$arSECTIONS[$arSection["ID"]]["COUNT"] += $currentCount;

	if ($arSection["IBLOCK_SECTION_ID"]>0 && isset($arSECTIONS[$arSection["IBLOCK_SECTION_ID"]])) {
		$arSection = $arSECTIONS[$arSection["IBLOCK_SECTION_ID"]];
		upCountParents($arSection, $arSECTIONS, $currentCount);
	}
}

//deb($cacheParams);
if ($this->StartResultCache($arParams["CACHE_TIME"], $cacheParams)) {
//if (1 == 1) {
	
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
	}
	else {
		
		$arResult['SECTIONS'] = array();		
				
		$arFilter= array(
				"IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
				/*"ACTIVE" => "Y"*/
		);
				
		$arSelect = array( 'ID', 'NAME', 'CODE', 'SORT', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL' );
		
		$rsSection = CIBlockSection::GetList(Array("left_margin"=>"ASC", "NAME"=>"ASC"), $arFilter, false, $arSelect);
		$curLevel = '';
		while ($data = $rsSection -> Fetch())
		{
			$data["COUNT"] = 0;
			$arResult['SECTIONS'][$data["ID"]] = $data;
			
			if ($data["IBLOCK_SECTION_ID"]>0) {
				$arResult['SECTIONS'][$data["IBLOCK_SECTION_ID"]]["CHILDS_IDS"][] = $data["ID"];
			}	
			
			if ($curLevel>0 && $data["DEPTH_LEVEL"] > $curLevel) {
				$arResult['SECTIONS'][$data["IBLOCK_SECTION_ID"]]["HAVE_CHILDS"] = "1";
			} 
			$curLevel = $data["DEPTH_LEVEL"];
		}
		
		$arSelect = array( 'ID', 'IBLOCK_SECTION_ID', 'NAME');
		
		$arFilter= array("IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"]);
		
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		
		$arResult['COUNT_PRODUCT_ALL'] = $rsElement->SelectedRowsCount();
		while ($data = $rsElement->Fetch()) {
			$arResult['SECTIONS'][$data["IBLOCK_SECTION_ID"]]["COUNT"] += 1;
		}
		
		// проставляем число товаров в разделах
		$i = 0;
		foreach ($arResult['SECTIONS'] as $id => $section) {

			if ($section["COUNT"] > 0 && !empty($section["IBLOCK_SECTION_ID"])) {
				upCountParents($arResult['SECTIONS'][$section["IBLOCK_SECTION_ID"]], $arResult['SECTIONS'], $section["COUNT"]);
			}			
		}		
		
		// задаем свойство число товаров в шапке
		if (!defined("COUNT_PRODUCTS_SET")) {
			$APPLICATION->SetPageProperty("count_products", $arResult['COUNT_PRODUCT_ALL']);
			define("COUNT_PRODUCTS_SET", 1);
		}
		//deb($arResult);
		$this -> IncludeComponentTemplate();
	}	
}	
?>
