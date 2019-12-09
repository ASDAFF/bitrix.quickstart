<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arrIDs = array();
if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0)
{
	foreach($arResult["ITEMS"] as $arItem)
	{
		$arrIDs[$arItem["ELEMENT_ID"]] = "Y";
	}
	?><script>RSGoPro_FAVORITE = <?=json_encode($arrIDs)?>;</script><?
}