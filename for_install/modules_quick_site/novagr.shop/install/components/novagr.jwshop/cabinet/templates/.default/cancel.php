<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
    "bitrix:sale.personal.order.cancel",
    "demoshop",
    array(
        "PATH_TO_LIST" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['orders'],
        "PATH_TO_DETAIL" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
        "SET_TITLE" => "N",
        "ID" => $arResult["VARIABLES"]["ID"],
    ),
    $component
);
?>
