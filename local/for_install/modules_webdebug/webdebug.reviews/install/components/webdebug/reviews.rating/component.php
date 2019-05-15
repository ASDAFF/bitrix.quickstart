<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// IBlockID & $ElementID
$IBlockID = IntVal($arParams["IBLOCK_ID"]);
if ($IBlockID<=0) return;
$ElementID = IntVal($arParams["ELEMENT_ID"]);
if ($ElementID==0 && trim($arParams["ELEMENT_CODE"])!="" && CModule::IncludeModule("iblock")) {
	$resElement = CIBlockElement::GetList(false,array("IBLOCK_ID"=>$IBlockID,"CODE"=>trim($arParams["ELEMENT_CODE"]),"ACTIVE"=>"Y"), false, false, array("IBLOCK_ID","ID"));
	if ($arElement = $resElement->GetNext(false,false)) {
		$ElementID = $arElement["ID"];
	}
}
if ($ElementID<=0) return;

// Get List
if (CModule::IncludeModule("webdebug.reviews")) {
	$arResult["RATING"] = CWebdebugReviews::GetAverageValue($ElementID, $arParams["VOTE"]);
	$arResult["MAX_RATING"] = $arParams["MAX_RATING"] && $arParams["MAX_RATING"]>0 ? $arParams["MAX_RATING"] : 5;
	$this->IncludeComponentTemplate();
} else return;

?>