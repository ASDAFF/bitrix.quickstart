<?
$arResult["TITLE"] = htmlspecialchars_decode($arResult["TITLE"]);
$arResult["ADDRESS"] = htmlspecialchars_decode($arResult["ADDRESS"]);
$arResult["ADDRESS"] = (strlen($arResult["TITLE"]) ? $arResult["TITLE"].', ' : '').$arResult["ADDRESS"];
$_SESSION['SHOP_TITLE'] = $arResult['ADDRESS'];
?>