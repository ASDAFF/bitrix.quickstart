<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(strlen($arResult["URL_TO_LIST"]) <= 0){
	$arResult["URL_TO_LIST"] = $arParams["PATH_TO_LIST"];
}