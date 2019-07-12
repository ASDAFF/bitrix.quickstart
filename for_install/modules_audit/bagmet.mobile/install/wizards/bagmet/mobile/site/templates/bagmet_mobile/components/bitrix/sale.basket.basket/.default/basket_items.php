<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo ShowError($arResult["ERROR_MESSAGE"]);?>

<div id="id-cart-list">
	<div class="sort_pannel">
		<div>
			<ul>
				<li class="sort_title"><?=GetMessage("SALE_PRD_IN_BASKET")?>:</li>
				<li class="sort_item"><a class="active" href="javascript:void(0)"><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?></a></li>
				<?if ($countItemsDelay=count($arResult["ITEMS"]["DelDelCanBuy"])):?><li class="sort_item"><a href="javascript:void(0)" onclick="ShowBasketItems(2);"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?> (<?=$countItemsDelay?>)</a></li><?endif?>
				<?if ($countItemsSubscribe=count($arResult["ITEMS"]["ProdSubscribe"])):?><li class="sort_item"><a href="javascript:void(0)" onclick="ShowBasketItems(3);"><?=GetMessage("SALE_PRD_IN_BASKET_SUBSCRIBE")?> (<?=$countItemsSubscribe?>)</a></li><?endif?>
				<?if ($countItemsNotAvailable=count($arResult["ITEMS"]["nAnCanBuy"])):?><li class="sort_item"><a href="javascript:void(0)" onclick="ShowBasketItems(4);"><?=GetMessage("SALE_PRD_IN_BASKET_NOTA")?> (<?=$countItemsNotAvailable?>)</a></li><?endif?>
			</ul>
		</div>
	</div>

<div class="table_wrapper">
<table class="shopping_cart data-table">
	<thead>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<th class="cart_tovar_img_title"><?= GetMessage("SALE_NAME")?></th>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_PROPS")?></th>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_PRICE")?></th>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_PRICE_TYPE")?></th>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_DISCOUNT")?></th>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_QUANTITY")?></th>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_DELETE")?></th>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_OTLOG")?></th>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_WEIGHT")?></th>
			<?endif;?>
		</tr>
	</thead>
	<tbody>
	<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
	{
		?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?><a class="cart_tovar_name" href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				?><p><?=$arBasketItems["NAME"] ?></p><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?></a><?
				endif;
				?></td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td>
				<?
				foreach($arBasketItems["PROPS"] as $val)
				{
					if ($val["CODE"] == "COLOR")
						echo "<p class='cart_tovar_other_p'>".$val["NAME"].": <img src='".$val["VALUE"]."'></p><br />";
					else
						echo "<p class='cart_tovar_other_p'>".$val["NAME"].":</p> ".$val["VALUE"]."<br />";
				}
				?>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td align="right" class="cart_tovar_price_cell"><p class='price'><?=$arBasketItems["PRICE_FORMATED"]?></p></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td align="center">
					<a href="javascript:void(0)" class="cart_tovar_qty_menos" onclick="if (BX('QUANTITY_<?=$arBasketItems["ID"]?>').value > 1) BX('QUANTITY_<?=$arBasketItems["ID"]?>').value--;"></a><input class="cart_tovar_qty" id="QUANTITY_<?=$arBasketItems["ID"]?>" maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3" ><a href="javascript:void(0)" onclick="BX('QUANTITY_<?=$arBasketItems["ID"]?>').value++;" class="cart_tovar_qty_plus"></a>
				</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td align="center">
					<!--<a class="delete_from_cart_btn" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a>-->
					<input type="checkbox" name="DELETE_<?=$arBasketItems["ID"] ?>" id="DELETE_<?=$i?>" value="Y">
				</td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><input type="checkbox" name="DELAY_<?=$arBasketItems["ID"] ?>" value="Y"></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td align="right"><?=$arBasketItems["WEIGHT_FORMATED"] ?></td>
			<?endif;?>
		</tr>
		<?
		$i++;
	}
	?>
	</tbody>
	<tfoot>
	<script>
	function sale_check_all(val)
	{
		for(i=0;i<=<?=count($arResult["ITEMS"]["AnDelCanBuy"])-1?>;i++)
		{
			if(val)
				document.getElementById('DELETE_'+i).checked = true;
			else
				document.getElementById('DELETE_'+i).checked = false;
		}
	}
	</script>
	<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td colspan="3" class="coupon_container">
				<div>
				<input type="text" name="COUPON" size="20"
					<?if(empty($arResult["COUPON"])):?>
						onclick="if (this.value=='<?=GetMessage("STB_COUPON_PROMT")?>')this.value=''; this.style.color='black'"
						onblur="if (this.value=='') {this.value='<?=GetMessage("STB_COUPON_PROMT")?>'; this.style.color='#a9a9a9'}"
						style="color:#a9a9a9"
					<?endif;?>
					  value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("STB_COUPON_PROMT")?><?endif;?>"
				>
				</div>
			</td>
		</tr>
	<?endif;?>
	<tr>
		<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
		<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<td>&nbsp;</td>
				<?endif;?>
				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<td>&nbsp;</td>
				<?endif;?>
				<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<td>&nbsp;</td>
				<?endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<td>&nbsp;</td>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<td >&nbsp;</td>
				<?endif;?>
				<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<td>&nbsp;</td>
				<?endif;?>
				<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
					<td align="right"><?=$arResult["allWeight_FORMATED"] ?></td>
				<?endif;?>
			<td align="right" nowrap>
				<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
					<p class="price_nds"><?echo GetMessage('SALE_VAT_INCLUDED')?></p>
				<?endif;?>
				<?
				if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
				{
					?><p class="price_nds"><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
					if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
						echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:</p><?
				}
				?>
				<p class="price_nds"><b><?= GetMessage("SALE_ITOGO")?></b>:</p>
			</td>
		<?endif;?>
		<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
			<td align="right" nowrap>
				<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
					<?=$arResult["allVATSum_FORMATED"]?><br />
				<?endif;?>
				<?
				if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
				{
					echo $arResult["DISCOUNT_PRICE_FORMATED"]."<br />";
				}
				?>
				<p class='price_total'><?=$arResult["allSum_FORMATED"]?></p>
			</td>
		<?endif;?>


	</tr>
	</tfoot>
</table>
	<div class="cart_submit_btns">
		<input class="cart_refresh_btn" type="submit" value="<?= GetMessage("SALE_REFRESH")?>" name="BasketRefresh">
		<input class="tovar_buy_button cart_buy_btn" type="submit" value="<?= GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton">
	</div>
</div>
<br />
<table width="100%">
	<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td colspan="3">
				
				<?= GetMessage("STB_COUPON_PROMT") ?>
				<input type="text" name="COUPON" value="<?=$arResult["COUPON"]?>" size="20">
				<br /><br />
			</td>
		</tr>
	<?endif;?>
	<tr>
		<td width="30%">
			<input type="submit" value="<?echo GetMessage("SALE_REFRESH")?>" name="BasketRefresh"><br />
			<small><?echo GetMessage("SALE_REFRESH_DESCR")?></small><br />
		</td>
		<td align="right" width="40%">&nbsp;</td>
		<td align="right" width="30%">
			<input type="submit" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2"><br />
			<small><?echo GetMessage("SALE_ORDER_DESCR")?></small><br />
		</td>
	</tr>
</table>

</div>
<?