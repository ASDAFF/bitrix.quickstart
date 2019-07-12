<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)){?>
<div id = "main_menu">
<ul>
<?foreach($arResult as $arItem){?>
	<?if($arItem["SELECTED"]){?>
		<li><a href="<?=$arItem["LINK"]?>" class="active color3"><?=$arItem["TEXT"]?></a></li>
	<?}else{?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	<?}?>
<?}?>
</ul>
</div>
<?}?>