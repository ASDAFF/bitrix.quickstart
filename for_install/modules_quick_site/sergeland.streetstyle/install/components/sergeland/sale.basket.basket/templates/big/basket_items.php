<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="cart-list">
<?$numCells = 0;?>
<table class="cart-header">
	<thead>
		<tr class="head">
			<?if(in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<th></th><th><?=GetMessage("SALE_NAME")?></th>
				<?$numCells += 2;?>
			<?endif;?>
			<?if(in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<th><?= GetMessage("SALE_VAT")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-weight"><?=GetMessage("SALE_WEIGHT")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-quantity"><?=GetMessage("SALE_QUANTITY")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-price"><?=GetMessage("SALE_PRICE")?></th>
				<?$numCells++;?>
			<?endif;?>
			<?if(in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-delay"><?=GetMessage("SALE_DELETE")?></th>
				<?$numCells++;?>
			<?endif;?>		
			<?if(in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-delay"><?=GetMessage("SALE_DELAY")?></th>
				<?$numCells++;?>
			<?endif;?>
		</tr>
	</thead>
	
	<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<tbody>
	<?$i=0; foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems):?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-img">
					<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><div class="img">
						<?if (!empty($arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"])) :?>
							<img src="<?=$arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
						<?endif?>
					</div></a>
				</td>
				<td class="cart-item-name">
					<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?=$arBasketItems["NAME"] ?></a>
					<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
						<div>
							<?if(is_array($arBasketItems["PROPS"]["COLOR"])):?><?=$arBasketItems["PROPS"]["COLOR"]["NAME"]?>: <?=$arBasketItems["PROPS"]["COLOR"]["VALUE"]?><br /><?endif?>						
							<?if(is_array($arBasketItems["PROPS"]["SIZE"])):?><?=$arBasketItems["PROPS"]["SIZE"]["NAME"]?>: <?=$arBasketItems["PROPS"]["SIZE"]["VALUE"]?><br /><?endif?>
							<?if(is_array($arBasketItems["PROPS"]["ARTNUMBER"])):?><?=$arBasketItems["PROPS"]["ARTNUMBER"]["NAME"]?>: <?=$arBasketItems["PROPS"]["ARTNUMBER"]["VALUE"]?><br /><?endif?>
						</div>
					<?endif?>
				</td>
			<?endif;?>
			<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td>
					<input type="text" class="quantity" maxlength="3" size="3" name="QUANTITY[<?=$arBasketItems["ID"]?>]" value="<?=$arBasketItems["QUANTITY"]?>">
					<div class="count_nav">
						<a href="#" class="plus">+</a>
						<a href="#" class="minus">-</a>
					</div>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price">
					<?if(doubleval($arBasketItems["DISCOUNT_PRICE_PERCENT"]) > 0):?>
						<div class="discount-price"><?=$arBasketItems["PRICE_FORMATED"]?></div>
						<div class="old-price"><?=$arBasketItems["FULL_PRICE_FORMATED"]?></div>
					<?else:?>
						<div class="price"><?=$arBasketItems["PRICE_FORMATED"];?></div>
					<?endif?>
				</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><input type="checkbox" name="DELETE[<?=$arBasketItems["ID"]?>]" value="Y"></td>
			<?endif;?>			
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><input type="checkbox" name="DELAY[<?=$arBasketItems["ID"]?>]" value="Y"></td>
			<?endif;?>
		</tr>
		<?$i++; endforeach?>
	</tbody>
</table>

<table class="cart-all">
	<tbody>
		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td rowspan="5" colspan="2">
				<input type="text" class="coupon" data-value="<?=GetMessage("SALE_COUPON_VAL")?>" value="<?=$arResult["COUPON"]?>" name="COUPON">
			</td>
		</tr>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<tr>
				<td><?echo GetMessage("SALE_ALL_WEIGHT")?>:</td>
				<td><?=$arResult["allWeight_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
			<tr>
				<td><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
					if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
						echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:
				</td>
				<td><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
			<tr>
				<td><?=GetMessage('SALE_VAT_EXCLUDED')?></td>
				<td><?=$arResult["allNOVATSum_FORMATED"]?></td>
			</tr>
			<tr>
				<td><?=GetMessage('SALE_VAT_INCLUDED')?></td>
				<td><?=$arResult["allVATSum_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<tr>
			<td colspan="2"><?=GetMessage("SALE_ITOGO")?>:&nbsp;&nbsp;<?=$arResult["allSum_FORMATED"]?></td>
		</tr>
	</tbody>
</table>
<br/>

<table class="cart-footer">
	<tr>
		<td><input type="submit" value="<?=GetMessage("SALE_UPDATE")?>" name="BasketRefresh" class="btn-submit"></td>
		<td align="right" width="40%"><?if(strlen($arResult["PREPAY_BUTTON"]) > 0) echo $arResult["PREPAY_BUTTON"];?></td>
		<td><input type="submit" value="<?=GetMessage("SALE_ORDER")?>" name="BasketOrder" class="btn-submit"></td>
	</tr>
</table>
<?else:?>
	<tbody>
		<tr>
			<td colspan="<?=$numCells?>">
				<div class="cart-notetext"><?=GetMessage("SALE_NO_ACTIVE_PRD");?></div>
				<a href="<?=SITE_DIR?>" class="bt3"><?=GetMessage("SALE_NO_ACTIVE_PRD_START")?></a><br><br>
			</td>
		</tr>
	</tbody>
</table>
<?endif;?>
</div>