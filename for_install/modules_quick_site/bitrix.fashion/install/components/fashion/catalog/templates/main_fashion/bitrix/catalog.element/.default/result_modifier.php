<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arInfo = CSiteFashionStore::ElementResModifier($arResult['OFFERS']);

$arResult['BASE_PRICES'] = $arInfo['BASE_PRICES'];
$arResult['DEFAULT_COLOR'] = $arInfo['DEFAULT_COLOR'];
$arResult['OFFERS_COMPACT'] = $arInfo['OFFERS_COMPACT'];

$arAddItems = CSiteFashionStore::ElementResModifierMore($arResult["IBLOCK_ID"], $arResult["PROPERTIES"]["similar_products"], $arResult["PROPERTIES"]["item_viewed"], $arResult["ID"], $arParams["SETS_IBLOCK_ID"], $arParams["OFFERS_ID"], $arParams["COLOR"], $arResult["DEFAULT_COLOR"], $arResult["OFFERS"], $arResult['OFFERS_COMPACT']);

$arResult['SIMILAR_PRODUCTS'] = $arAddItems['SIMILAR_PRODUCTS'];
$arResult['VIEWED_PRODUCTS'] = $arAddItems['VIEWED_PRODUCTS'];
$arResult['VIEW_PRODUCTS'] = $arAddItems['VIEW_PRODUCTS'];
$arResult['SET'] = $arAddItems['SET'];
$arResult['SETS'] = $arAddItems['SETS'];