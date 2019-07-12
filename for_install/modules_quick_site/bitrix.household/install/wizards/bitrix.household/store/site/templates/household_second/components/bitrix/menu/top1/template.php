<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="menu_m">
<table width="100%" id="menu" cellpadding="0" cellspacing="0" border="0">
<tr>
<?
$i=0;
foreach($arResult as $arItem):
$i++;
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<?if($arItem["SELECTED"]):?>
		<td class="<?if ($i==1) echo "first2"; else if ($i==count($arResult)) echo "final2";?>"><div class="menu_s"><a href="<?=$arItem["LINK"]?>" class="selected"><?=$arItem["TEXT"]?></a></div></td>
	<?else:?>
		<td class="<?if ($i==1) echo "first2"; else if ($i==count($arResult)) echo "final2";?>"><div class="menu_s"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div></td>
	<?endif?>
	
<?endforeach?>
</tr>
</table>
<div class="clear"></div>
</div>
<?endif?>

					
			