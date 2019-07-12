<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="current-brand">
	<?if(is_array($arResult["PIC"])){?><div class="img"><img src="<?=$arResult["PIC"]["src"]?>" alt="" /></div><?}?>
	<div class="descrip text">
		<p><?echo $arResult["PREVIEW_TEXT"];?></p>
	</div>
</div>

