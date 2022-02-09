<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
			<div class="b-footer-section">
				<div class="b-footer__title">Разделы сайта</div>
				<table class="b-footer-section__table">
					<tr>
<?
$nav_count = count($arResult);
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
<?if(reset($arResult)==$arItem or $nav_count/$arItem["ITEM_INDEX"]==2):?>
<td class="b-footer-section__td">
<?endif;?>
	<?if($arItem["SELECTED"]):?>
		<a href="<?=$arItem["LINK"]?>" class="b-footer-section__link selected"><?=$arItem["TEXT"]?></a>
	<?else:?>
		<a href="<?=$arItem["LINK"]?>" class="b-footer-section__link"><?=$arItem["TEXT"]?></a>
	<?endif?>
<?if(end($arResult)==$arItem  or $nav_count/($arItem["ITEM_INDEX"]+1)==2):?>
</td>
<?endif;?>
<?endforeach?>

					</tr>
				</table>
			</div>
<?endif?>