<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	$data = json_decode($_POST["DATA"], true);
	$basketItems = $data["BASKET_ITEMS"];
	$discountPrice = $data["DISCOUNT_PRICE"];
	$discountPercentFormatted = $data["DISCOUNT_PERCENT_FORMATED"];
	$discountPriceFormatted = $data["DISCOUNT_PRICE_FORMATED"];
	$deliveryPrice = $data["DELIVERY_PRICE"];
	$totalPriceFormatted = $data["ORDER_TOTAL_PRICE_FORMATED"];
	$deliveryPriceFormatted = $data["DELIVERY_PRICE_FORMATED"];
	$orderPriceFormatted = $data["ORDER_PRICE_FORMATED"];
	$orderWeightFormatted = $data["ORDER_WEIGHT_FORMATED"];
?>
<div class="order-form">
	<h4><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></h4>
	<table class="table-summary">
		<tr>
			<th align="right"><?=GetMessage("SOA_TEMPL_SUM_NAME")?></th>
			<th align="right"><?=GetMessage("SOA_TEMPL_SUM_QUANTITY")?></th>
			<th align="right"><?=GetMessage("SOA_TEMPL_SUM_PRICE")?></th>
		</tr>
	
		<? foreach($basketItems as $arBasketItems): ?>
			<tr>
				<td align="right"><?=$arBasketItems["NAME"]?></td>
				<td align="right"><?=$arBasketItems["QUANTITY"]?></td>
				<td align="right"><?=$arBasketItems["PRICE_FORMATED"]?></td>
			</tr>
		<? endforeach; ?>
	
	
		<? if(doubleval($discountPrice) > 0): ?>
			<tr>
				<td align="right">
					<b>
						<?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?>
						<?if (strLen($discountPercentFormatted)>0):?> 
							(<?echo $discountPercentFormatted;?>)
						<?endif;?>
						:
					</b>
				</td>
				<td align="right" colspan="6">
					<? echo $discountPriceFormatted; ?>
				</td>
			</tr>
		<? endif; ?>
	
		<? if(!empty($arResult["arTaxList"])): ?>
			<? foreach($arResult["arTaxList"] as $val): ?>
				<tr>
					<td align="right"><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</td>
					<td align="right" colspan="6"><?=$val["VALUE_MONEY_FORMATED"]?></td>
				</tr>
			<? endforeach; ?>
		<? endif; ?>

		<? if(doubleval($deliveryPrice) > 0): ?>
			<tr>
				<td align="right">
					<b><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></b>
				</td>
				<td align="right" colspan="6"><?=$deliveryPriceFormatted;?></td>
			</tr>
		<? endif; ?>

		<tr>
			<td align="right">
				<b><?=GetMessage("SOA_TEMPL_SUM_IT")?></b>
			</td>
			<td align="right" colspan="6">
				<b><?=$totalPriceFormatted;?></b>
			</td>
		</tr>
		<? if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0): ?>
			<tr>
				<td align="right"><b><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></b></td>
				<td align="right" colspan="6"><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></td>
			</tr>
		<? endif; ?>
	</table>
</div>

<div class="order-form">
	<h4><?=GetMessage("SOA_TEMPL_SUM_ADIT_INFO")?></h4>
	<table>
		<tr>
			<td width="50%" align="left" valign="top"><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?>
				<textarea rows="4" cols="40" name="ORDER_DESCRIPTION">
					<?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?>
				</textarea>
			</td>
		</tr>
	</table>
</div>