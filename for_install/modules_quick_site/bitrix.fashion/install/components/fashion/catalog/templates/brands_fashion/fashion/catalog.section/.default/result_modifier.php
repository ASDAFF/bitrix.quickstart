<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$arInfo = CSiteFashionStore::SectionResModifier($arResult["ITEMS"]);

$arResult["BASE_PRICES"] = $arInfo['BASE_PRICES'];
$arResult["ITEMS"] = $arInfo['ITEMS'];
?>