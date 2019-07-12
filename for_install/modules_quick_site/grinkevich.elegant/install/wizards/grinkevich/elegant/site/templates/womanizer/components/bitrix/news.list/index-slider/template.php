<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="slider" class="nivo-slider">
	<?foreach($arResult["ITEMS"] as $arItem){
		?><a href="<?=$arItem["PROPERTIES"]["LINK"][VALUE]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" /></a><?
	}?>
</div>


