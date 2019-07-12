<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul>
<?$APPLICATION->AddBufferContent("ShowSort");?>

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<li><a href="<?=$arItem["LINK"]?>" class="<?=($arItem["SELECTED"] ? 'selected' : '')?>  <?=($arItem["PARAMS"]["class"] ? $arItem["PARAMS"]["class"] : "")?>" ><?=$arItem["TEXT"]?></a></li>

	 
	
<?endforeach?>

</ul>
<?endif?>