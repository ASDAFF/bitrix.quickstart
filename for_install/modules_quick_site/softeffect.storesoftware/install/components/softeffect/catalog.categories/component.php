<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "sw_catalog";

if($arParams["IBLOCK_TYPE"]=="-")
	$arParams["IBLOCK_TYPE"] = "";

if(!is_array($arParams["IBLOCK"]))
	$arParams["IBLOCK"] = array($arParams["IBLOCK"]);

foreach($arParams["IBLOCK"] as $k=>$v)
	if(!$v)
		unset($arParams["IBLOCK"][$k]);

$arParams["CATEGORIES_URL"] = trim($arParams["CATEGORIES_URL"]);
if(strlen($arParams["BRANDS_URL"])<=0)
 	$arParams["CATEGORIES_URL"] = SITE_DIR."catalog/category/";

if($this->StartResultCache(FALSE, FALSE)) {
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult=array('CATEGORIES_URL'=>$arParams["CATEGORIES_URL"], 'SECTIONS'=>array());

	$arOrder = Array("SORT"=>"ASC", 'NAME'=>'ASC');
	$arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK"], 'ACTIVE'=>'Y');
	$arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'CODE', 'PROPERTY_CATEGORY_NAME');
	$res = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelect);
	while($ob_res = $res->GetNext()) {
		$arResult['SECTIONS'][] = array('NAME'=>($ob_res['PROPERTY_CATEGORY_NAME_VALUE']!='') ? $ob_res['PROPERTY_CATEGORY_NAME_VALUE'] : $ob_res['NAME'], 'CODE'=>$ob_res['CODE']);
	}
	
	$this->IncludeComponentTemplate();
}
?>
