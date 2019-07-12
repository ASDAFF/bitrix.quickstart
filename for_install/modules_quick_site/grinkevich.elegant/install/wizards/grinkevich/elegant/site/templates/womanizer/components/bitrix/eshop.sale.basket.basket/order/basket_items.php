<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="cart-table">

<?$numCells = 0;?>

<table class="" rules="rows" width="100%">
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
		</tr>
	</thead>
<?$numCells++;?>
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>

	<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
	{
		?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="td-prod">
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
					<?endif;?>
					<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
						<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?else:?>
						<img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?endif?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<span><?=$arBasketItems["NAME"] ?></span>
						</a>
					<?endif;?>
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
					<strong><?=$arBasketItems[QUANTITY]?></strong>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price">
					<strong class="red"><span class="basketprice"><?=$arBasketItems["TOTAL_PRICE"]?></span></strong>
				</td>
			<?endif;?>
		</tr>
		<?
		$i++;
	}
	?>

</table>

<?else:?>
	<tbody>
		<tr>
			<td colspan="<?=$numCells?>" style="text-align:center">
				<div class="cart-notetext"><?=GetMessage("SALE_NO_ACTIVE_PRD");?></div>
				<a href="<?=SITE_DIR?>" class="bt3"><?=GetMessage("SALE_NO_ACTIVE_PRD_START")?></a><br><br>
			</td>
		</tr>
	</tbody>
</table>
<?endif;?>


</div>