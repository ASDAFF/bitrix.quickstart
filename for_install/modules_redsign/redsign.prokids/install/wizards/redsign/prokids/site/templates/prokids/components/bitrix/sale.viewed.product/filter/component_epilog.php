<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arrAlreadyViewed;
$arrAlreadyViewed = array();
if(is_array($arResult) && count($arResult)>0)
{
	foreach($arResult as $arItem)
	{
		$arrAlreadyViewed["ID"][] = $arItem["PRODUCT_ID"];
	}
}