<?php

global $APPLICATION;
// свойства страницы
$userName = $arResult["USER"]["TITLE"];
if($arParams["SET_TITLE"] == "Y") {
	$APPLICATION->SetTitle($userName);
}
if($arParams["ADD_ELEMENT_CHAIN"] == "Y") {

	$APPLICATION->AddChainItem($userName);
}