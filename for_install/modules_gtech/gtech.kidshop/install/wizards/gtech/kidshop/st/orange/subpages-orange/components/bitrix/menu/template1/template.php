<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul class="left-menu">

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<?if($arItem["SELECTED"]):?>
		<li class="selected"><span class="li-right"><span class="li-left"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></span></span></li>
	<?else:?>
		<li><span class="li-right"><span class="li-left"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></span></span></li>
	<?endif?>
	
<?endforeach?>

</ul>
<?endif?>