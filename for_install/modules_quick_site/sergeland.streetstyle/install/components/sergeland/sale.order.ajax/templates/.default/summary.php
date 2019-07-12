<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="section">
<div class="title"><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></div>

<table class="sale_data-table summary">
<thead>
	<tr>
		<th>&nbsp;</th>
		<th><?=GetMessage("SOA_TEMPL_SUM_NAME")?></th>
		<th><?=GetMessage("SOA_TEMPL_SUM_PROPS")?></th>
		<th><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?></th>
		<th><?=GetMessage("SOA_TEMPL_SUM_QUANTITY")?></th>
		<th class="price"><?=GetMessage("SOA_TEMPL_SUM_PRICE")?></th>
	</tr>
</thead>
<tbody>
	<?foreach($arResult["BASKET_ITEMS"] as $arBasketItems):?>
		<tr>
			<td>
				<?
				if (count($arBasketItems["DETAIL_PICTURE"]) > 0)
					echo CFile::ShowImage($arBasketItems["DETAIL_PICTURE"], $arParams["DISPLAY_IMG_WIDTH"], $arParams["DISPLAY_IMG_HEIGHT"], "border=0", "", false);
				elseif (count($arBasketItems["PREVIEW_PICTURE"]) > 0)
					echo CFile::ShowImage($arBasketItems["PREVIEW_PICTURE"], $arParams["DISPLAY_IMG_WIDTH"], $arParams["DISPLAY_IMG_HEIGHT"], "border=0", "", false);
				?>
			</td>
			<td class="name"><?=$arBasketItems["NAME"]?></td>
			<td>
				<?foreach($arBasketItems["PROPS"] as $val)
					$arBasketItems["~PROPS"][$val["CODE"]] = $val?>

				<?if(is_array($arBasketItems["~PROPS"]["COLOR"])):?><?=$arBasketItems["~PROPS"]["COLOR"]["NAME"]?>: <?=$arBasketItems["~PROPS"]["COLOR"]["VALUE"]?><br /><?endif?>						
				<?if(is_array($arBasketItems["~PROPS"]["SIZE"])):?><?=$arBasketItems["~PROPS"]["SIZE"]["NAME"]?>: <?=$arBasketItems["~PROPS"]["SIZE"]["VALUE"]?><br /><?endif?>
				<?if(is_array($arBasketItems["~PROPS"]["ARTNUMBER"])):?><?=$arBasketItems["~PROPS"]["ARTNUMBER"]["NAME"]?>: <?=$arBasketItems["~PROPS"]["ARTNUMBER"]["VALUE"]?><br /><?endif?>
			</td>
			<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<td><?=$arBasketItems["QUANTITY"]?></td>
			<td class="price"><?=$arBasketItems["PRICE_FORMATED"]?></td>
		</tr>
	<?endforeach?>
</tbody>
<tfoot>
	<tr class="">
		<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></td>
		<td class="price itog"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></td>
	</tr>
	<tr>
		<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_SUMMARY")?></td>
		<td class="price itog"><?=$arResult["ORDER_PRICE_FORMATED"]?></td>
	</tr>
	<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
		<tr>
			<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>:</td>
			<td class="price itog"><?echo $arResult["DISCOUNT_PRICE_FORMATED"]?></td>
		</tr>
	<?endif?>	
	<?if(!empty($arResult["arTaxList"])):?>
		<?foreach($arResult["arTaxList"] as $val):?>
			<tr>
				<td colspan="5" class="itog"><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</td>
				<td class="price itog"><?=$val["VALUE_MONEY_FORMATED"]?></td>
			</tr>
		<?endforeach?>
	<?endif?>	
	<?if (doubleval($arResult["DELIVERY_PRICE"]) > 0):?>
		<tr>
			<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>
			<td class="price itog"><?=$arResult["DELIVERY_PRICE_FORMATED"]?></td>
		</tr>
	<?endif?>	
	<?if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0):?>
		<tr>
			<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></td>
			<td class="price itog"><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></td>
		</tr>
	<?endif?>
	<tr class="last">
		<td colspan="5" class="itog"><?=GetMessage("SOA_TEMPL_SUM_IT")?></td>
		<td class="price itog"><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></td>
	</tr>
</tfoot>
</table>

<br /><br />
<div class="title"><?=GetMessage("SOA_TEMPL_SUM_ADIT_INFO")?></div>

<table class="sale_order_table">
	<tr>
		<td class="order_comment">
			<div><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></div>
			<textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
		</td>
	</tr>
</table>
</div>