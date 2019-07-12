<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<div id="cart-table">

<table class="myorders_itog">
	<tr>
		<td><?=GetMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></td>
		<td><?=GetMessage("SOA_TEMPL_SUM_SUMMARY")?></td>
	<?
	if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
	{
		?>
			<td><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>:</td>
		<?
	}
	if(!empty($arResult["arTaxList"]))
	{
		foreach($arResult["arTaxList"] as $val)
		{
			?>
				<td><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</td>
			<?
		}
	}
	if (doubleval($arResult["DELIVERY_PRICE"]) > 0)
	{
		?>
			<td><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>
		<?
	}
	?>
		<td><?=GetMessage("SOA_TEMPL_SUM_IT")?></td>
	<?
	if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
	{
		?>
			<td><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></td>
		<?
	}
	?>




	<tr>
		<td><span class="basketprice"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></span></td>
		<td><strong><span class="basketprice"><?=$arResult["ORDER_PRICE_FORMATED"]?></span></strong></td>
	<?
	if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
	{
		?>
			<td><span class="basketprice"><?echo $arResult["DISCOUNT_PRICE_FORMATED"]?></span></td>
		<?
	}
	if(!empty($arResult["arTaxList"]))
	{
		foreach($arResult["arTaxList"] as $val)
		{
			?>
				<td><span class="basketprice"><?=$val["VALUE_MONEY_FORMATED"]?></span></td>
			<?
		}
	}
	if (doubleval($arResult["DELIVERY_PRICE"]) > 0)
	{
		?>
			<td><strong><span class="basketprice"><?=$arResult["DELIVERY_PRICE_FORMATED"]?></span></strong></td>
		<?
	}
	?>
		<td><strong class="red"><span class="basketprice"><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></span></strong></td>
	<?
	if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
	{
		?>
			<td><span class="basketprice"><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></span></td>
		<?
	}
	?>



</table>


</div>