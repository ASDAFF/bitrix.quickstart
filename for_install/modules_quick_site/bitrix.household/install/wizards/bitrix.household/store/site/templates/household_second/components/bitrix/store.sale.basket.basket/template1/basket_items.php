<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>




<div class="cart-items" id="id-cart-list">
	<div class="tabs">
		<ul class="technical">
			<li><a href="javascript:void(0)" class="active"><b><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?></b></a></li>&nbsp;
			<li><a href="javascript:void(0)" onclick="ShowBasketItems(2);"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?> (<?=count($arResult["ITEMS"]["DelDelCanBuy"])?>)</a></li>&nbsp;
			<?if(false):?>
			<li><a href="javascript:void(0)" onclick="ShowBasketItems(3);"><?=GetMessage("SALE_PRD_IN_BASKET_NOTA")?> (<?=count($arResult["ITEMS"]["nAnCanBuy"])?>)</a></li>
			<?endif;?>
		</ul>
	</div>
	<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<table class="cart-items table_polos" cellspacing="0">
	<thead>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-name"><b><?= GetMessage("SALE_NAME")?></b></th>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-price"><b><?= GetMessage("SALE_PRICE")?></b></th>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-type"><b><?= GetMessage("SALE_PRICE_TYPE")?></b></th>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-discount"><b><?= GetMessage("SALE_DISCOUNT")?></b></th>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-weight"><b><?= GetMessage("SALE_WEIGHT")?></b></th>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-quantity"><b><?= GetMessage("SALE_QUANTITY")?></b></th>
			<?endif;?>
			<th class="cart-item-actions">
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"]) || in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<b><?= GetMessage("SALE_ACTION")?></b>
				<?endif;?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
	{
		?>
		<tr>
		<?if ($i%2==0):?>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-name">
				<?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				?><?=$arBasketItems["NAME"] ?><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?></a><?
				endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
				{
					foreach($arBasketItems["PROPS"] as $val)
					{
						echo "<br />".$val["NAME"].": ".$val["VALUE"];
					}
				}?>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price"><?=$arBasketItems["PRICE_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-type"><?=$arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-discount"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-weight"><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-quantity"><input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3"></td>
			<?endif;?>
			<td class="cart-item-actions">
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<a class="cart-delete-item" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"><?=GetMessage("SALE_DELETE")?></a>
				<?endif;?>
				<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<a class="cart-shelve-item" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>"><?=GetMessage("SALE_OTLOG")?></a>
				<?endif;?>
			</td>
		</tr>
	<?else:?>
	<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-name">
				<?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				?><?=$arBasketItems["NAME"] ?><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?></a><?
				endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
				{
					foreach($arBasketItems["PROPS"] as $val)
					{
						echo "<br />".$val["NAME"].": ".$val["VALUE"];
					}
				}?>
				</th>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-price"><?=$arBasketItems["PRICE_FORMATED"]?></th>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-type"><?=$arBasketItems["NOTES"]?></th>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-discount"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></th>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-weight"><?=$arBasketItems["WEIGHT_FORMATED"]?></th>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<th class="cart-item-quantity"><input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3"></th>
			<?endif;?>
			<th class="cart-item-actions">
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<a class="cart-delete-item" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"><?=GetMessage("SALE_DELETE")?></a>
				<?endif;?>
				<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<a class="cart-shelve-item" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>"><?=GetMessage("SALE_OTLOG")?></a>
				<?endif;?>
			</th>
		</tr>
	<?endif;?>
		<?
		$i++;
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-name">
					<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
						<p><?echo GetMessage("SALE_ALL_WEIGHT")?>:</p>
					<?endif;?>
					<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
					{
						?><p><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
						if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
							echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:</p><?
					}?>
					<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
						<p><?echo GetMessage('SALE_VAT_INCLUDED')?></p>
					<?endif;?>
					<p><?= GetMessage("SALE_ITOGO")?>:</p>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price">
					<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
						<p><?=$arResult["allWeight_FORMATED"]?></p>
					<?endif;?>
					<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
						<p><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></p>
					<?endif;?>
					<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
						<p><?=$arResult["allVATSum_FORMATED"]?></p>
					<?endif;?>
					<p><?=$arResult["allSum_FORMATED"]?></p>
				</td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-type">&nbsp;</td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-discount">&nbsp;</td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-weight">&nbsp;</td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-quantity">&nbsp;</td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"]) || in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-actions">&nbsp;</td>
			<?endif;?>
		</tr>
	</tfoot>
	</table>

	<div class="cart-ordering">
		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
			<br/><div class="cart-code">
				<input size="50" <?if(empty($arResult["COUPON"])):?>onclick="if (this.value=='<?=GetMessage("SALE_COUPON_VAL")?>')this.value=''" onblur="if (this.value=='')this.value='<?=GetMessage("SALE_COUPON_VAL")?>'"<?endif;?> value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SALE_COUPON_VAL")?><?endif;?>" name="COUPON">
			</div>
		<?endif;?>
		<div class="cart-buttons">
			<br/><input type="submit" value="<?echo GetMessage("SALE_UPDATE")?>" name="BasketRefresh">
			<input type="submit" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2">
		</div>
	</div>
	<?else:
		echo ShowNote(GetMessage("SALE_NO_ACTIVE_PRD"));
	endif;?>
</div>
<?