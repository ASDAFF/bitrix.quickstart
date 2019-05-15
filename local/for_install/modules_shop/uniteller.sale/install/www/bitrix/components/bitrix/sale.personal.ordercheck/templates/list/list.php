<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
// UnitellerPlugin change
	'bitrix:sale.personal.ordercheck.list',
	'list',
// /UnitellerPlugin change
	array(
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
		"PATH_TO_COPY" => $arResult["PATH_TO_LIST"].'?ID=#ID#',
// UnitellerPlugin add
		'PATH_TO_CHECK' => $arResult['PATH_TO_CHECK'] . '?ID=#ID#',
// /UnitellerPlugin add
		"PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],
		"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],
		"ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ID" => $arResult["VARIABLES"]["ID"],
		"NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],
	),
	$component
);
?>
