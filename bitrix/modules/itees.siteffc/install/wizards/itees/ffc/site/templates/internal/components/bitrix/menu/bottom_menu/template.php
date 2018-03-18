<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)){?>
<div id = "footer_menu">
<ul>
<?foreach($arResult as $key=>$arItem){?>
	<?if($arItem["SELECTED"]){?>
		<li><a href="<?=$arItem["LINK"]?>" class="active color3"><?=$arItem["TEXT"]?></a></li>
	<?}else{?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	<?}?>
<?if($key !== count($arResult)-1){?>
<li>/</li>
<?}?>
<?}?>
</ul>
</div>
<?}?>