<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<table cellpadding="0" cellspacing="0" border="0" height="38px"><tr>
	<?foreach($arResult as $arItem):?>
		<td style="padding: 0 20px 0 20px;" <?if($arItem["SELECTED"]){?>class="mainmenu-selected"<?}else{?>class="mainmenu-item"<?}?>>
			<a href="<?=$arItem['LINK']?>" class="mainmenu-link"><?=$arItem["TEXT"]?></a>
		</td>
	<?endforeach;?>
</tr></table>
<?endif?>