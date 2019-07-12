<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(count($arResult["SECTIONS"])>0){
	$count = 5;
	if(intval($arParams["COUNT_SECTIONS"])>0)
		$count = $arParams["COUNT_SECTIONS"];
	$full_width = 1000;
	$block_width = 0;
	$rest = 0;
	if(count($arResult["SECTIONS"])>$count)
		$arResult["SECTIONS"] = array_slice($arResult["SECTIONS"], 0, $count);
		
	if(fmod($full_width, count($arResult["SECTIONS"]))>0){
		$block_width = $full_width / count($arResult["SECTIONS"]);
		$block_width = floor($block_width);
		$rest = $full_width - $block_width*count($arResult["SECTIONS"]);
	}else{
		$block_width = $full_width / count($arResult["SECTIONS"]);
	}
	$arResult["REST"] = $rest;
	$arResult["BLOCK_WIDTH"] = $block_width;
}
?>