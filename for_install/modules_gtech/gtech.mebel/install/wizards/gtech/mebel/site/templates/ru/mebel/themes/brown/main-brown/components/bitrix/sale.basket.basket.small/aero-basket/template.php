<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script>
$(document).ready(function(){
	$("#aero-basket-button").click(function(){
		$("div.aero-basket-inner").slideToggle();
		$(this).toggleClass("aero-basket-button-up");
	});
});
</script>

<div class="aero-basket">
<div class="aero-basket-inner">
<div class="aero-basket-inner-shadow">&nbsp;</div>
	<?$quantity = "нет";?>
	<?if($arResult["ITEMS"]){$quantity=0;?>
	<table cellpadding="0" cellspacing="0" border="0" width="270px" align="center">
		<?foreach($arResult["ITEMS"] as $arItem){if($arItem["DELAY"]=="N"){$quantity++; $total = $total+$arItem["PRICE"]*$arItem["QUANTITY"];?>
			<tr>
				<td class="aero-basket-item" colspan="2"><b><?=$arItem["NAME"]?></b></br><small><?=GetMessage("TSBS_PRICE")?> <?=$arItem["PRICE"]?> <span class="rouble">c</span>&nbsp;&nbsp;&nbsp;<?=GetMessage("TSBS_QUANTITY")?> <?=$arItem["QUANTITY"]?></small></td>
			</tr>
		<?}}?>
			<tr><td class="aero-basket-item" style="padding-top:10px;" colspan="2"><?=GetMessage("TSBS_TOTAL")?> <b><?=$total?></b> <span class="rouble">c</span></td></tr>
			<tr>
				<td width="50%" style="padding-bottom:10px;">
					<a href="<?=$arParams["PATH_TO_ORDER"]?>"><img src="<?=$templateFolder?>/images/aero-basket-button-toorder.png" border="0"></a>
				</td>
				<td style="padding-bottom:10px;">
					<a href="<?=$arParams["PATH_TO_BASKET"]?>"><img src="<?=$templateFolder?>/images/aero-basket-button-tobasket.png" border="0"></a>
				</td>
			</tr>
	</table>
	<?}else{?>
		<p style="margin:0; padding:0 0 10px 20px;"><?=GetMessage("TSBS_EMPTY")?></p>
	<?}?>
</div>
<div class="aero-basket-bookmark">
	<table cellpadding="0" cellspacing="0" border="0" width="270px" align="center"><tr>
		<td align="left" height="25px" valign="middle" class="aero-basket-button-img"><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="aero-basket-link" title="Перейти в корзину"><?=GetMessage("TSBS_INBASKET")?> <?=$quantity?> <?=GetMessage("TSBS_ITEMS")?></a></td>
		<td id="aero-basket-button" class="aero-basket-button-down">&nbsp;</td>
	</tr></table>
</div>
</div>