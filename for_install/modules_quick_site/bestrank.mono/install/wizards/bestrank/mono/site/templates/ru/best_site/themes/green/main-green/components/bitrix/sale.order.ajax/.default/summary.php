<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></h2>
<table class="equipment" rules="rows"  >
    <thead>
        <tr>
            <td><?=GetMessage("SOA_TEMPL_SUM_NAME")?></td><td></td>
            <td><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?></td>
            <td><?=GetMessage("SOA_TEMPL_SUM_QUANTITY")?></td>
            <td><?=GetMessage("SOA_TEMPL_SUM_PRICE")?></td>
        </tr>
    </thead>
	<?
	foreach($arResult["BASKET_ITEMS"] as $arBasketItems)
	{
		?>
		<tr>
			<td align="center"><?if($arBasketItems['DETAIL_PICTURE']):?>
				<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
			<?endif;?></td>
			<td class="product_name"><?=$arBasketItems["NAME"]?></td>
			<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<td><?=$arBasketItems["QUANTITY"]?></td>
			<td class="prices" align="right"><nobr><?=$arBasketItems["PRICE_FORMATED"]?></nobr></td>
		</tr>
		<?
	}
	?>
</table>

<table class="myorders_itog">
	<tr>
		<td><?=GetMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></td>
		<td><?=$arResult["ORDER_WEIGHT_FORMATED"]?></td>
	</tr>
	<tr>
		<td><?=GetMessage("SOA_TEMPL_SUM_SUMMARY")?></td>
		<td><?=$arResult["ORDER_PRICE_FORMATED"]?></td>
	</tr>
	<?
	if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
	{
		?>
		<tr>
			<td><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>:</td>
			<td><?echo $arResult["DISCOUNT_PRICE_FORMATED"]?>
			</td>
		</tr>
		<?
	}
	if(!empty($arResult["arTaxList"]))
	{
		foreach($arResult["arTaxList"] as $val)
		{
			?>
			<tr>
				<td><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</td>
				<td><?=$val["VALUE_MONEY_FORMATED"]?></td>
			</tr>
			<?
		}
	}
	if (doubleval($arResult["DELIVERY_PRICE"]) > 0)
	{
		?>
		<tr>
			<td><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>
			<td><?=$arResult["DELIVERY_PRICE_FORMATED"]?></td>
		</tr>
		<?
	}
	?>
	<tr>
		<td><?=GetMessage("SOA_TEMPL_SUM_IT")?></td>
		<td><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></td>
	</tr>
	<?
	if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
	{
		?>
		<tr>
			<td><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></td>
			<td><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></td>
		</tr>
		<?
	}
	?>
</table>
