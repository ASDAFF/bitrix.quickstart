<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->AddChainItem(GetMessage("TITLE_CANCEL"));
$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.cancel",
	"cancel",
	array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"SET_TITLE" => "Y",
		"ID" => $arResult["VARIABLES"]["ID"],
	),
	$component
);
?>
