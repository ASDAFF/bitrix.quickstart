<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);
unset($arResult["ITEMS"]);
unset($arResult["PRICES"]);
unset($arResult["HIDDEN"]);

echo json_encode($arResult);
?>