<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Mlife\Asz as ASZ;
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

//echo'<pre>';print_r($arParams);echo'</pre>';

if(!CModule::IncludeModule('mlife.asz')) {
		return;
}

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

if($_REQUEST["ajaxsmallbasket"]==1){
$APPLICATION->restartBuffer();
}

$arResult = array();

$ASZ_USER = ASZ\BasketUserFunc::getAszUid();

$arResult["ORDER"] = array();
$arResult["ORDER"]["ITEMSUM"] = 0;
$arResult["ORDER"]["ITEMDISCOUNT"] = 0;
$arResult["ORDER"]["CNT"] = 0;

if(intval($ASZ_USER)>0) {
	
	$arResult["SHOW_BASKET"] = true;
	
	$arResult["BASE_CURENCY"] = Asz\CurencyFunc::getBaseCurency(SITE_ID);
	
	$res = ASZ\BasketTable::getList(
		array(
			'select' => array("*"),
			'filter' => array("USERID"=>$ASZ_USER,"ORDER_ID"=>false)
		)
	);
	$arProd = array();
	$arResult["BASKET_ITEMS"] = array();
	while($arRes = $res->Fetch()){
		if(!$arRes["DISCOUNT_VAL"]) $arRes["DISCOUNT_VAL"] = 0;
		if(!$arRes["DISCOUNT_CUR"]) $arRes["DISCOUNT_CUR"] = $arResult["BASE_CURENCY"];
		$arResult["ORDER"]["ITEMSUM"] = $arResult["ORDER"]["ITEMSUM"] + (Asz\CurencyFunc::convertBase($arRes["PRICE_VAL"],$arRes["PRICE_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["ORDER"]["ITEMDISCOUNT"] = $arResult["ORDER"]["ITEMDISCOUNT"] + (Asz\CurencyFunc::convertBase($arRes["DISCOUNT_VAL"],$arRes["DISCOUNT_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["ORDER"]["CNT"] = $arResult["ORDER"]["CNT"]+ $arRes["QUANT"];
	}
	$arResult["ORDER"]["ITEMSUMFIN"] = $arResult["ORDER"]["ITEMSUM"] - $arResult["ORDER"]["ITEMDISCOUNT"];
	$arResult["ORDER"]["ITEMSUMFIN_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUMFIN"],false,SITE_ID);
	
}else{
	$arResult["SHOW_BASKET"] = false;
}

$this->IncludeComponentTemplate();

if($_REQUEST["ajaxsmallbasket"]==1){
die();
}

?>