<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="left-menu">

<?foreach($arResult as $arItem):?>
	<?if($arItem["SELECTED"]):?>
		<div class="bl"><div class="br"><div class="tl"><div class="tr"><a href="<?=$arItem["LINK"]?>" class="selected"><?=$arItem["TEXT"]?></a></div></div></div></div>
	<?else:?>
		<div class="bl"><div class="br"><div class="tl"><div class="tr"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div></div></div></div>
	<?endif?>
	
<?endforeach?>

</div>
<?endif?>