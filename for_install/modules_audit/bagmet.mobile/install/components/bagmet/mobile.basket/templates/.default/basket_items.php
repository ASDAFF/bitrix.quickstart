<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo ShowError($arResult["ERROR_MESSAGE"]);?>

<div id="id-cart-list">
	<div class="catalog-section-list">
		<ul  class="cart_sections">
			<li class="cart_sections_title"><?=GetMessage("SHOES_PRD_IN_BASKET")?>:</li>
			<li><a class="active" href="javascript:void(0)"><?=GetMessage("SHOES_PRD_IN_BASKET_ACT")?></a></li>
			<?if ($countItemsDelay=count($arResult["ITEMS"]["DelDelCanBuy"])):?><li><a href="javascript:void(0)" onclick="ShowBasketItems(2);"><?=GetMessage("SHOES_PRD_IN_BASKET_SHELVE")?> (<?=$countItemsDelay?>)</a></li><?endif?>
			<?if ($countItemsSubscribe=count($arResult["ITEMS"]["ProdSubscribe"])):?><li><a href="javascript:void(0)" onclick="ShowBasketItems(3);"><?=GetMessage("SHOES_PRD_IN_BASKET_SUBSCRIBE")?> (<?=$countItemsSubscribe?>)</a></li><?endif?>
			<?if ($countItemsNotAvailable=count($arResult["ITEMS"]["nAnCanBuy"])):?><li><a href="javascript:void(0)" onclick="ShowBasketItems(4);"><?=GetMessage("SHOES_PRD_IN_BASKET_NOTA")?> (<?=$countItemsNotAvailable?>)</a></li><?endif?>
		</ul>
	</div>

<div class="table_wrapper">
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<table class="shopping_cart shopping_cart_row_height">
		<thead>
			<tr>
				<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<th class="cart_tovar_img_title"><?= GetMessage("SHOES_NAME")?></th>
					<th></th>
				<?endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_PROPS")?></th>
				<?endif;?>

				<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_PRICE_TYPE")?></th>
				<?endif;?>
				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_DISCOUNT")?></th>
				<?endif;?>
				<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_WEIGHT")?></th>
				<?endif;?>
				<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_QUANTITY")?></th>
				<?endif;?>
				<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
					<th><?= GetMessage("SHOES_PRICE")?></th>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
						<th></th>
					<?endif;?>
				<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<th></th>
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
					<td  class="cart_tovar_img_cell">
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>" class="cart_tovar_img">
						<?endif;?>
						<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
							<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
						<?else:?>
							<img src="/bitrix/components/bagmet/mobile.basket/templates/.default/images/nothing_found.png" alt="<?=$arBasketItems["NAME"] ?>"/>
						<?endif?>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							</a>
						<?endif;?>
					</td>
					<td>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							<a class="cart_tovar_name" href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
						<?endif;?>
						<p><?=$arBasketItems["NAME"] ?></p>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							</a>
						<?endif;?>
					</td>
				<?endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<td align="center">
					<?
					foreach($arBasketItems["PROPS"] as $val)
					{
						echo "<p class='cart_tovar_other_p'>".$val["NAME"].": ".$val["VALUE"]."</p>";
					}
					?>
					</td>
				<?endif;?>

				<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<td><p class="cart_tovar_qty"><?=$arBasketItems["NOTES"]?></p></td>
				<?endif;?>
				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<td><p class="cart_tovar_qty"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></p></td>
				<?endif;?>
				<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
					<td align="right" nowrap><p class="cart_tovar_qty"><?=$arBasketItems["WEIGHT_FORMATED"] ?></p></td>
				<?endif;?>
				<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<td align="center">
						<a href="javascript:void(0)" class="cart_tovar_qty_menos" onclick="if (BX('QUANTITY_<?=$arBasketItems["ID"]?>').value > 1) BX('QUANTITY_<?=$arBasketItems["ID"]?>').value--;"></a><input class="cart_tovar_qty" id="QUANTITY_<?=$arBasketItems["ID"]?>" maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3" ><a href="javascript:void(0)" onclick="BX('QUANTITY_<?=$arBasketItems["ID"]?>').value++;" class="cart_tovar_qty_plus"></a>
					</td>
				<?endif;?>
				<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
					<td align="right" class="cart_tovar_price_cell"><p class='price'><?=$arBasketItems["PRICE_FORMATED"]?></p></td>
				<?endif;?>
				<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<td align="center" class="cart_tovar_reserve_cell"><a href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>" class="reserve_from_cart_btn"><?=GetMessage("SHOES_OTLOG")?></a></td>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<td align="center">
						<a class="delete_from_cart_btn" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SHOES_DELETE_PRD")?>"></a>
					</td>
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
							onclick="if (this.value=='<?=GetMessage("SHOES_COUPON_PROMT")?>')this.value=''; this.style.color='black'"
							onblur="if (this.value=='') {this.value='<?=GetMessage("SHOES_COUPON_PROMT")?>'; this.style.color='#a9a9a9'}"
							style="color:#a9a9a9"
						<?endif;?>
						  value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SHOES_COUPON_PROMT")?><?endif;?>"
					>
					</div>
				</td>
			</tr>
		<?endif;?>

	<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td></td>
				<td></td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="price_descr_container" nowrap>
					<p class="price_nds_title">
						<?echo GetMessage("SHOES_CONTENT_DISCOUNT");
						if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
							echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:
					</p>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td align="right" class="cart_tovar_price_cell" nowrap>
						<p class="price_nds"><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></p>
				</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
		</tr>
	<?endif;?>
	<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td></td>
				<td></td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="price_descr_container" nowrap>
					<p class="price_nds_title"><?echo GetMessage('SHOES_VAT_INCLUDED')?></p>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td align="right" class="cart_tovar_price_cell" nowrap>
						<p class="price_nds"><?=$arResult["allVATSum_FORMATED"]?></p>
				</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
		</tr>
	<?endif;?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td></td>
				<td></td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="price_nds_title" nowrap>
					<b><?= GetMessage("SHOES_ITOGO")?>:</b>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td align="right" class="cart_tovar_price_cell" nowrap>
					<p class='price_total'><?=$arResult["allSum_FORMATED"]?></p>
				</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td></td>
			<?endif;?>
		</tr>
		</tfoot>
	</table>
	<div class="cart_submit_btns">
		<input class="cart_refresh_btn" type="submit" value="<?= GetMessage("SHOES_REFRESH")?>" name="BasketRefresh">
		<input class="tovar_buy_button cart_buy_btn" type="submit" value="<?= GetMessage("SHOES_ORDER")?>" name="BasketOrder"  id="basketOrderButton">
	</div>
<?else:?>
	<p class="empty_cart_title"><?=GetMessage("SHOES_EMPTY_CART")?></p>
	<img class="empty_cart" src="/bitrix/components/bagmet/mobile.basket/templates/.default/images/shopping-cart.png">
<?endif?>
</div>
</div>
<?