<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// Default params
$arParams["REVIEWS_COUNT"] = IntVal($arParams["REVIEWS_COUNT"])>0 ? IntVal($arParams["REVIEWS_COUNT"]) : 10;
if (!isset($arParams["DISPLAY_FIELDS"]) || !is_array($arParams["DISPLAY_FIELDS"]) || empty($arParams["DISPLAY_FIELDS"])) $arParams["DISPLAY_FIELDS"] = array("NAME", "EMAIL", "TEXT_PLUS", "TEXT_MINUS", "TEXT_COMMENTS", "VOTE_0", "VOTE_1", "VOTE_2");
$arParams["USE_MODERATE"] = $arParams["USE_MODERATE"]=="N" ? "N" : "Y";
$arParams["EMAIL_PUBLIC"] = $arParams["EMAIL_PUBLIC"]=="N" ? "N" : "Y";

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

// Get IBLock Elemtent name
$arResult["ELEMENT_NAME"] = "";
if (CModule::IncludeModule("iblock")) {
	$resItem = CIBlockElement::GetList(false, array("IBLOCK_ID"=>$IBlockID,"ID"=>$ElementID), false, false, array("IBLOCK_ID", "NAME"));
	if ($arItem = $resItem->GetNext(false,false)) {
		$arResult["ELEMENT_NAME"] = $arItem["NAME"];
	}
}

for ($i=0; $i<10; $i++) {
	$arResult["VOTE_NAME_".$i] = COption::GetOptionString("webdebug.reviews", "vote_name_".$i);
}

// Get List
if (CModule::IncludeModule("webdebug.reviews")) {
	$arSort = array("ID"=>"DESC");
	$arFilter = array("IBLOCK_ID"=>$IBlockID,"ELEMENT_ID"=>$ElementID);
	if ($arParams["USE_MODERATE"]!="N") $arFilter["MODERATED"] = "Y";
	$arGroupBy = false;
	$arSelect = false;
	$resReviews = CWebdebugReviews::GetList($arSort, $arFilter, $arGroupBy, $arSelect);
	$resReviews->NavStart($arParams["REVIEWS_COUNT"], $arParams["PAGER_SHOW_ALL"]=="Y" ? true : false);
	$arResult["ITEMS"] = array();
	while ($arReview = $resReviews->GetNext(false,false)) {
		if ($arReview["DATETIME"]) {
			$arReview["DATETIME"] = CDatabase::FormatDate($arReview["DATETIME"], "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("SHORT"));
		}
		if (trim($arReview["TEXT_PLUS"])!="") $arReview["TEXT_PLUS"] = nl2br($arReview["TEXT_PLUS"]);
		if (trim($arReview["TEXT_MINUS"])!="") $arReview["TEXT_MINUS"] = nl2br($arReview["TEXT_MINUS"]);
		if (trim($arReview["TEXT_COMMENTS"])!="") $arReview["TEXT_COMMENTS"] = nl2br($arReview["TEXT_COMMENTS"]);
		if ($arReview["WWW"]) {
			$arReview["URL"] = $arReview["WWW"];
			if (substr($arReview["URL"],0,5)!="http:" && substr($arReview["URL"],0,6)!="https:") {
				$arReview["URL"] = "http://".$arReview["URL"];
			}
		}
		$arResult["ITEMS"][] = $arReview;
	}
	$arResult["NAV_STRING"] = $resReviews->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]=="Y"?true:false);
	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
	$arResult["NAV_RESULT"] = $resReviews;
	$this->IncludeComponentTemplate();
} else return;

?>