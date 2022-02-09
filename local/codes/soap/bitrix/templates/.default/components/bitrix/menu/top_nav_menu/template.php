<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<nav class="b-header-menu clearfix">

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<?if($arItem["SELECTED"]):?>
		<a href="<?=$arItem["LINK"]?>" class="b-header-menu__link selected"><span><?=$arItem["TEXT"]?></span></a>
	<?else:?>
		<a href="<?=$arItem["LINK"]?>" class="b-header-menu__link"><span><?=$arItem["TEXT"]?></span></a>
	<?endif?>
	
<?endforeach?>

</nav>
<?endif?>