<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
// UnitellerPlugin change
	'bitrix:sale.personal.ordercheck.cancel',
// /UnitellerPlugin change
	"",
	array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ID" => $arResult["VARIABLES"]["ID"],
	),
	$component
);
?>
