<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arResult["~SKU"] = array();

if($arResult["IS_SKU"])
{
	foreach($arResult["SKU"] as $arItem)
	{		
		 $arResult["~SKU"][$arItem["ID"]]["ID"] = $arItem["ID"];
		 $arResult["~SKU"][$arItem["ID"]]["URL"] = $arItem["URL"];
		 $arResult["~SKU"][$arItem["ID"]]["TITLE"] = $arItem["TITLE"];
		 $arResult["~SKU"][$arItem["ID"]]["PHONE"] = $arItem["PHONE"];
		 $arResult["~SKU"][$arItem["ID"]]["SCHEDULE"] = $arItem["SCHEDULE"];
		 $arResult["~SKU"][$arItem["ID"]]["NUM_AMOUNT"] += $arItem["NUM_AMOUNT"];
	}
	foreach($arResult["~SKU"] as &$arItem)
	{
		if($arParams["USE_MIN_AMOUNT"] == 'Y')
		{
			if(intval($arItem["NUM_AMOUNT"]) >= $arParams["MIN_AMOUNT"])
				$arItem["AMOUNT"] = GetMessage("LOT_OF_GOOD");
			elseif(intval($arItem["NUM_AMOUNT"]) == 0)
				$arItem["AMOUNT"] = GetMessage("ABSENT");
			elseif(intval($arItem["NUM_AMOUNT"]) < $arParams["MIN_AMOUNT"])
				$arItem["AMOUNT"] = GetMessage("NOT_MUCH_GOOD");
		}
		else $arItem["AMOUNT"] = $arItem["NUM_AMOUNT"];
	}
	$arResult["SKU"] = $arResult["~SKU"];
}
?>