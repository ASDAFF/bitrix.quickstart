<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="order-item">
	<div class="order-title">
		<b class="r2"></b><b class="r1"></b><b class="r0"></b>
		<div class="order-title-inner">
			<span><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></span>
		</div>
	</div>
	<div class="order-info">

	<table class="cart-items" cellspacing="0">
	<thead>
		<tr>
				<td class="cart-item-name"><?= GetMessage("SOA_TEMPL_SUM_NAME")?></td>
				<td class="cart-item-price"><?= GetMessage("SOA_TEMPL_SUM_PRICE")?></td>
				<td class="cart-item-price"><?= GetMessage("SOA_TEMPL_VAT")?></td>
				<td class="cart-item-quantity"><?= GetMessage("SOA_TEMPL_SUM_QUANTITY")?></td>
		</tr>
	</thead>
	<tbody>
	<?
	foreach($arResult["BASKET_ITEMS"] as $arBasketItems)
	{
		?>
		<tr>
			<td class="cart-item-name"><?=$arBasketItems["NAME"]?>
				<?
				foreach($arBasketItems["PROPS"] as $val)
				{
					echo "<br />".$val["NAME"].": ".$val["VALUE"];
				}
				?>
			</td>
			<td class="cart-item-price"><?=$arBasketItems["PRICE_FORMATED"]?></td>
			<td class="cart-item-price"><?=$arBasketItems["VAT_RATE"]*100?>%</td>
			<td class="cart-item-quantity"><?=$arBasketItems["QUANTITY"]?></td>
		</tr>
		<?
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td class="cart-item-name">
				<?if (doubleval($arResult["ORDER_WEIGHT"]) > 0):?>
					<p><?=GetMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></p>
				<?endif;?>
				<p><?=GetMessage("SOA_TEMPL_SUM_SUMMARY")?></p>
				<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
					<p><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>:</p>
				<?endif;?>
				<?if(!empty($arResult["arTaxList"]))
				{
					foreach($arResult["arTaxList"] as $val)
					{
						?>
						<p><?=$val["NAME"]?>:</p>
						<?
					}
				}?>
				<?if (doubleval($arResult["DELIVERY_PRICE"]) > 0):?>
					<p><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></p>
				<?endif;?>
				<p><b><?=GetMessage("SOA_TEMPL_SUM_IT")?></b></p>
				<?if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0):?>
					<p><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></p>
				<?endif;?>
			</td>
			
			<td class="cart-item-price">
				<?if (doubleval($arResult["ORDER_WEIGHT"]) > 0):?>
					<p><?=$arResult["ORDER_WEIGHT_FORMATED"]?></p>
				<?endif;?>
					
				<p><?= SaleFormatCurrency($arResult["ORDER_PRICE"] - $arResult["VAT_SUM"], $arResult["BASE_LANG_CURRENCY"])?></p>

				<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
					<p><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></p>
				<?endif;?>
				<?if(!empty($arResult["arTaxList"])):?>
					<p></p>
				<?endif;?>
				<?if(!empty($arResult["arTaxList"]))
				{
					foreach($arResult["arTaxList"] as $val)
					{
						?>
						<p><?=$val["VALUE_MONEY_FORMATED"]?></p>
						<?
					}
				}?>
				<?if (doubleval($arResult["DELIVERY_PRICE"]) > 0):?>
					<p><?=$arResult["DELIVERY_PRICE_FORMATED"]?></p>
				<?endif;?>
				<p><b><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></b></p>
				<?if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0):?>
					<p><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></p>
				<?endif;?>
			</td>
			<td class="cart-item-quantity">&nbsp;</td>
			<td class="cart-item-quantity">&nbsp;</td>
		</tr>
	</tfoot>
</table>
</div></div>

<div class="order-item">
	<div class="order-title">
		<b class="r2"></b><b class="r1"></b><b class="r0"></b>
		<div class="order-title-inner">
			<span><?=GetMessage("SOA_TEMPL_SUM_ADIT_INFO")?></span>
		</div>
	</div>
	<div class="order-info">
		<div align="center">
			<textarea rows="7" name="ORDER_DESCRIPTION" style="width:95%;"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
		</div>
	</div>
</div>