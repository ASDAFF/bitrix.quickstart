<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<div>&nbsp;</div>

<div id="id-cart-list">

	<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<? /*
	<div class="sort">
		<div class="sorttext"><?=GetMessage("SALE_PRD_IN_BASKET")?>
			<a href="javascript:void(0)" class="sortbutton current"><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?></a>
			<?if ($countItemsDelay=count($arResult["ITEMS"]["DelDelCanBuy"])):?>| <a href="javascript:void(0)" onclick="ShowBasketItems(2);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?> (<?=$countItemsDelay?>)</a><?endif?>
			<?if ($countItemsSubscribe=count($arResult["ITEMS"]["ProdSubscribe"])):?>| <a href="javascript:void(0)" onclick="ShowBasketItems(3);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_SUBSCRIBE")?> (<?=$countItemsSubscribe?>)</a><?endif?>
			<?if ($countItemsNotAvailable=count($arResult["ITEMS"]["nAnCanBuy"])):?>| <a href="javascript:void(0)" onclick="ShowBasketItems(4);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_NOTA")?> (<?=$countItemsNotAvailable?>)</a><?endif?>
		</div>
	</div>
	<br />
	*/ ?>
	<? endif; ?>

<?$numCells = 0;?>
<div id="cart-table">

	<table class="equipment mycurrentorders" rules="rows" width="100%">
			<thead>
				<tr>
					<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
						<th><?= GetMessage("SALE_NAME")?></th>
						<?$numCells += 1;?>
					<?endif;?>
					<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
						<th><?= GetMessage("SALE_VAT")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-price"><?= GetMessage("SALE_PRICE")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-quantity"><?= GetMessage("SALE_QUANTITY")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
						<th class="cart-item-price"><?= GetMessage("SALE_TOTAL_PRICE")?></th>
						<?$numCells++;?>
					<?endif;?>
					<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
						<!--<td class="cart-item-delay"></td>-->
						<?//$numCells++;?>
					<?endif;?>
						<th></th>
				</tr>
			</thead>
			<?$numCells++;?>


			<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
				<tbody>
				<?
				$i=0;
				foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
				{
					?>
					<tr>
						<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
							<td class="cart-item-name td-prod">
								<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
									<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
								<?endif;?>

								<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
									<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
								<?else:?>
									<img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>"/>
								<?endif?>

			                        <span><?=$arBasketItems["NAME"] ?></span>

								<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
									</a>
								<?endif;?>



								<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
								{
									foreach($arBasketItems["PROPS"] as $val)
									{
										echo "<br />".$val["NAME"].": ".$val["VALUE"];
									}
								}?>
							</td>
						<?endif;?>
						<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
							<td><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
						<?endif;?>
						<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
							<td><?=$arBasketItems["NOTES"]?></td>
						<?endif;?>
						<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
							<td><?=(strlen($arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]) > 0 ? $arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"] : "нет" )?></td>
						<?endif;?>
						<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
							<td><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
						<?endif;?>
						<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
							<td class="cart-item-price">
								<strong>
								<?if(doubleval($arBasketItems["FULL_PRICE"]) > 0):?>
									<span class="basketprice"><?=$arBasketItems["PRICE_FORMATED"]?></span><br />
									<span class="basketoldprice"><?=$arBasketItems["FULL_PRICE_FORMATED"]?></span>
								<?else:?>
									<span class="basketprice"><?=$arBasketItems["PRICE_FORMATED"];?></span>
								<?endif?>
								</strong>
							</td>
						<?endif;?>
						<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
							<td>
								<!--<a href="#" class="munisQuantity" id="<?=$arBasketItems["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/k_minus.png" alt="" /></a> -->
								<input type="text" size="3" class="coltext" id="QUANTITY_<?=$arBasketItems["ID"]?>" name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems[QUANTITY]?>" />
								<!-- <a href="#" class="plusQuantity" id="<?=$arBasketItems["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/k_plus.png" alt="" /></a>-->
							</td>
						<?endif;?>
						<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
							<td class="cart-item-price">
								<strong class="red"><span class="basketprice"><?=$arBasketItems["TOTAL_PRICE"]?></span></strong>
							</td>
						<?endif;?>
						<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
							<!--<td><a id="moveBasketProd" href="#" data-id="<?=$arBasketItems["ID"]?>" data-action="shelve"><?=GetMessage("SALE_OTLOG")?></a></td>-->
						<?endif;?>
							<td><a href="/personal/cart/?action=delete&id=<?=$arBasketItems["ID"]?>" id="delBasketProd" data-id="<?=$arBasketItems["PRODUCT_ID"]?>">
								<img src="<?=SITE_TEMPLATE_PATH?>/images/del_ico.png" alt="" /></a>&nbsp;
							</td>
					</tr>
					<?
					$i++;
				}
				?>
				</tbody>
				<?else:?>
					<tbody>
						<tr>
							<td colspan="<?=$numCells?>" style="text-align:center">
								<br />
								<div class="cart-notetext"><?=GetMessage("SALE_NO_ACTIVE_PRD");?></div>
								<a href="<?=SITE_DIR?>" class="bt3"><?=GetMessage("SALE_NO_ACTIVE_PRD_START")?></a><br><br>
							</td>
						</tr>
					</tbody>
				<?endif;?>
	</table>

	<div class="border"></div>

</div>



<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
<table class="myorders_itog" width="100%">
	<tbody>
		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td rowspan="5" class="tal">
				<input size="30" class="coltext1"
					<?if(empty($arResult["COUPON"])):?>
						onclick="if (this.value=='<?=GetMessage("SALE_COUPON_VAL")?>')this.value=''; this.style.color='black'"
						onblur="if (this.value=='') {this.value='<?=GetMessage("SALE_COUPON_VAL")?>'; this.style.color='#a9a9a9'}"
						style="color:#a9a9a9"
					<?endif;?>
						value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SALE_COUPON_VAL")?><?endif;?>"
						name="COUPON">
			</td>
		</tr>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<tr>
				<td><?echo GetMessage("SALE_ALL_WEIGHT")?>:</td>
				<td align="right"><span class="basketprice"><?=$arResult["allWeight_FORMATED"]?></span></td>
			</tr>
		<?endif;?>
		<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
			<tr>
				<td><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
					if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
						echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:
				</td>
				<td align="right"><span class="basketprice"><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></span></td>
			</tr>
		<?endif;?>
		<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
			<tr>
				<td><?echo GetMessage('SALE_VAT_EXCLUDED')?></td>
				<td align="right"><span class="basketprice"><?=$arResult["allNOVATSum_FORMATED"]?></span></td>
			</tr>
			<tr>
				<td><?echo GetMessage('SALE_VAT_INCLUDED')?></td>
				<td align="right"><span class="basketprice"><?=$arResult["allVATSum_FORMATED"]?></span></td>
			</tr>
		<?endif;?>
		<tr>
			<td colspan="2">
				<div id="total">
					<?= GetMessage("SALE_ORDER_TOTAL_PRICE"); ?><strong><?=$arResult["allSum_FORMATED"]?></strong>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<br/>


<div id="cart-foot" style="margin-top: 10px;">
	<div id="cf-butts">
		<a class="button orange" href="<?=$arParams["PATH_TO_ORDER"]?>"><span><?= GetMessage("SALE_ORDER_CONTINUE"); ?></span></a>
		<input type="submit" value="<?echo GetMessage("SALE_UPDATE")?>" name="BasketRefresh" class="BasketRefreshBtn">
	</div>
</div>
<? endif; ?>



</div>




<?