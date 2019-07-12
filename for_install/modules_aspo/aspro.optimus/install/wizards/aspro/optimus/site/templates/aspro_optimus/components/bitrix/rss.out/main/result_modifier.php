<?
foreach($arResult["ITEMS"] as $key=>$arItem){
	$arTmpYear = explode(' ', $arItem["ELEMENT"]["ACTIVE_FROM"]);
	$year = explode('.', $arTmpYear[0]);
	$arResult["ITEMS"][$key]["link"] = str_replace('#YEAR#', $year[2], $arItem["link"]);
}
?>