<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams["FONT_MIN"] = intVal($arParams["FONT_MIN"]) > 0 ? $arParams["FONT_MIN"] : 10;
$arParams["FONT_MAX"] = intVal($arParams["FONT_MAX"]) > 0 ? $arParams["FONT_MAX"] : 20;

if(!is_array($arResult["SEARCH"]) && empty($arResult["SEARCH"])) return;
	foreach ($arResult["SEARCH"] as $key => $res)
		$arResult["SEARCH"][$key]["FONT_SIZE"] = mt_rand($arParams["FONT_MIN"], $arParams["FONT_MAX"]);
?>