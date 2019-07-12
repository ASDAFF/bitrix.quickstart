<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0) {
	$arrIDs = array();
	foreach($arResult["ITEMS"] as $arItem) {
		$arrIDs[$arItem["PRODUCT_ID"]] = ( $arItem['CATALOG']['PARENT_ID']>0 ? $arItem['CATALOG']['PARENT_ID'] : 'Y' );
	}
	?><script>RSGoPro_INBASKET = <?=json_encode($arrIDs)?>;</script><?
}