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

$arParams["MAX_COUNT"] = intval($arParams["MAX_COUNT"]);
if($arParams["MAX_COUNT"]<=0)
	$arParams["MAX_COUNT"] = 15;

$arParams["BRANDS_URL"] = trim($arParams["BRANDS_URL"]);
if(strlen($arParams["BRANDS_URL"])<=0)
 	$arParams["BRANDS_URL"] = SITE_DIR."brands/";

if($this->StartResultCache(FALSE, FALSE)) {
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult=array('MAX_COUNT'=>$arParams["MAX_COUNT"], 'BRANDS_URL'=>$arParams["BRANDS_URL"], 'SECTIONS'=>array());

	$res = CIBlockSection::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arParams["IBLOCK"], 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => '1'));
	while ($arRes = $res->GetNext()) {
	    $arResult['SECTIONS'][] = array($arRes['NAME'], $arRes['SECTION_PAGE_URL'], $arRes['CODE']);
	}
	
	$arResult['SECTION_SELECTED'] = $arParams['CATALOG_SECTION'];
	
	$this->IncludeComponentTemplate();
}
?>
