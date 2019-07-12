<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult)):?>
<ul>
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?><li><a href="<?=$arItem["LINK"]?>" ><i class="icon-thumbs-up"></i> <?=$arItem["TEXT"]?></a></li>	
<?endforeach?>
</ul>
<?endif?>