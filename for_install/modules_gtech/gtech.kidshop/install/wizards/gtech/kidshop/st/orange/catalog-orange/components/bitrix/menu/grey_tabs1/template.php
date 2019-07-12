<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<table cellpadding="0" cellspacing="0" border="0" style="margin-left:-10px;"><tr>
<?foreach($arResult as $arItem):?>
	<?if ($arItem["PERMISSION"] > "D"):?>
		<td>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" <?if($arItem["SELECTED"]){?>class="selected"<?}?>><tr>
				<td class="mainmenu-left">&nbsp;</td>
				<td class="mainmenu-bg"><a class="mainmenu-link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></td>
				<td class="mainmenu-right">&nbsp;</td>
			</tr></table>
		</td>
	<?endif?>
<?endforeach?>
</tr></table>
<?endif?>