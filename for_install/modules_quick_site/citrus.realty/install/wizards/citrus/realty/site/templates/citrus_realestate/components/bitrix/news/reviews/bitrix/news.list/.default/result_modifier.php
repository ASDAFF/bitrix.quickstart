<?
$grouped = array();
foreach ($arResult["ITEMS"] as $key=>$arItem)
{
	$dateTime = ParseDateTime($arItem["ACTIVE_FROM"]);
	$grouped[$dateTime["MM"] . '.' . $dateTime['YYYY']][] = $arItem;
}
$arResult["GROUPED"] = $grouped;