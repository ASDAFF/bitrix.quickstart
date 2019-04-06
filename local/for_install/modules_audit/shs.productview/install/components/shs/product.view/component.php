<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BCF_MODULE_NOT_INSTALLED"));
	return;
}
if(!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("CC_CATALOG_MODULE_NOT_INSTALLED"));
	return;
}
if(!CModule::IncludeModule("shs.productview"))
{
	ShowError(GetMessage("CC_SHS_PRODUCTVIEW_NOT_INSTALLED"));
	return;
}
if(!CModule::IncludeModule("statistic"))
{
	ShowError(GetMessage("CC_STATISTIC_MODULE_NOT_INSTALLED"));
	return;
}
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

$arElement = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>$arParams["OFFERS_IBLOCK_ID"], "ID"=>$arParams["ELEMENT_ID"], "ACTIVE"=>"Y"), false, array("nTopCount"=>1), array("ID", "DETAIL_PAGE_URL"))->GetNext();
$arFilter = array("URL_LAST"=>$arElement["DETAIL_PAGE_URL"]);
//pr($arFilter);
$rsData = CUserOnline::GetList($guest_count, $session_count, false, $arFilter);
$arResult["COUNT"] = -1;
while($arData = $rsData->Fetch())
{   //pr($arData);
    $arResult["COUNT"]++;
}
//pr($arResult);
if($arResult["COUNT"]<=0) return;
if($arParams["JQUERY"]=="Y")$APPLICATION->AddHeadScript("https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js");
$this->IncludeComponentTemplate();
?>
