<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

$arParams["MAX_COUNT"] = intval($arParams["MAX_COUNT"]);
if($arParams["MAX_COUNT"]<=0)
	$arParams["MAX_COUNT"] = 10;


if(!CModule::IncludeModule("sale")) {
	$this->AbortResultCache();
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALLED"));
	return;
}

$arResult=array('VIEWED'=>array());

$db_res = CSaleViewedProduct::GetList(array("DATE_VISIT" => "DESC"), $arFilter, false, array("nTopCount" => $arParams["MAX_COUNT"]), array('ID', 'IBLOCK_ID', 'PRICE', 'CURRENCY', 'CAN_BUY', 'PRODUCT_ID', 'DATE_VISIT', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'NAME', 'NOTES'));
while ($arItems = $db_res->Fetch()) {
	$arResult['VIEWED'][$arItems["PRODUCT_ID"]] = $arItems;
}

$this->IncludeComponentTemplate();
?>
