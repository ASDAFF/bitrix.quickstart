<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<?
$i=0;
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
	?>
	<?if($i>0):?>
	     &nbsp;&nbsp;|&nbsp;&nbsp;
	<?endif?>
    <?$i++;?>
	
	<?if($arItem["SELECTED"]):?>
		<span><?=$arItem["TEXT"]?></span>
	<?else:?>
		<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
	<?endif?>		
	
<?endforeach?>
<?endif?>