<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<h1><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_1")?></h1>
<div class="basketWrap">
<?if($_REQUEST['ajaxrefresh']==1){
global $APPLICATION;
$APPLICATION->restartBuffer();
}?>
<?
if($arResult["SHOW_BASKET"] && !$arResult['ORDER_CREATE']){
?>
<form method="post" id="orderForm" action="<?=$APPLICATION->GetCurPage(false)?>">
<input type="hidden" name="ajaxrefresh" id="ajaxrefresh" value=""/>
<p style="color:#ffffff;overflow:hidden;height:1px;">
.............. ................. ................. .................. ..............
.............. ................. ................. .................. ..............
.............. ................. ................. .................. ..............
</p>
	<table class="basketItems">
		<tr>
			<th class="photo"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_2")?></th>
			<th class="name"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_3")?></th>
			<th class="price"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_4")?></th>
			<th class="quant"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_5")?></th>
			<th class="priceall"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_6")?></th>
			<th class="delete"></th>
		</tr>
		<?foreach($arResult["BASKET_ITEMS"] as $item){?>
		<tr id="p<?=$item["ID"]?>">
			<td class="photo">
				<a href="<?=$arResult["PROD"][$item["PROD_ID"]]["DETAIL_PAGE_URL"]?>">
					<?if(!$arResult["PROD"][$item["PROD_ID"]]["IMG_SRC"]){?>
					<?$arResult["PROD"][$item["PROD_ID"]]["IMG_SRC"] = $templateFolder."/images/no_photo.jpg"?>
					<?}?>
					<img src="<?=$arResult["PROD"][$item["PROD_ID"]]["IMG_SRC"]?>"/>
				</a>
			</td>
			<td class="name">
				<div class="itemName">
					<a href="<?=$arResult["PROD"][$item["PROD_ID"]]["DETAIL_PAGE_URL"]?>">
					<?=$item["PROD_NAME"]?>
					</a>
				</div>
				<div class="prodDesc">
					<?=$item["PROD_DESC"]?>
				</div>
			</td>
			<td class="price">
				<?=$item["PRICE_DISPLAY"]?>
				<?if($item["DISCOUNT_VAL"]>0){?>
				<br/><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_SKID")?>: <?=$item["DISCOUNT_DISPLAY"]?>
				<?}?>
			</td>
			<td class="quant">
				<input type="text" name="quant" value="<?=intval($item["QUANT"])?>"/>
				<div class="buttonPl">
					<a href="#" class="plus" data-id="<?=$item["ID"]?>" data-prod="<?=$item["PROD_ID"]?>">+</a>
					<a href="#" class="minus" data-id="<?=$item["ID"]?>" data-prod="<?=$item["PROD_ID"]?>">-</a>
					<a href="#" class="update" data-id="<?=$item["ID"]?>" data-prod="<?=$item["PROD_ID"]?>">0</a>
				</div>
				<?if(isset($arResult["QUANT"][$item["PROD_ID"]]) && intval($arResult["QUANT"][$item["PROD_ID"]])<$item["QUANT"]){
				$tovarZakaz = true;
				?>
				<div class="zakazItem"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_ZAKAZ")?>: <?echo ($item["QUANT"] - intval($arResult["QUANT"][$item["PROD_ID"]]))?> <?=GetMessage("MLIFE_ASZ_BASKET_FULL_ZAKAZ_I")?></div>
				<?}?>
			</td>
			<td class="priceall">
				<?=$item["PRICE_DISPLAY_ALL"]?>
			</td>
			<td class="delete">
				<a href="#" class="delete" data-id="<?=$item["ID"]?>" data-prod="<?=$item["PROD_ID"]?>">X</a>
			</td>
		</tr>
		<?}?>
	</table>
	<div class="clear"></div>
	<?if($tovarZakaz && $arParams["ZAKAZ"]=="Y" && strlen($arParams["ZAKAZ_TEXT"])>0){?>
		<p class="zakaz"><?=$arParams["ZAKAZ_TEXT"]?></p>
	<?}?>
	<div class="orderPriceAll">
		<div class="wrapPriceAll">
			<div class="left"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_7")?>:</div>
			<div class="right"><?=$arResult["ORDER"]["ITEMSUM_DISPLAY"]?></div>
		</div>
		<div class="wrapPriceAll">
			<div class="left"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_8")?>:</div>
			<div class="right"><?=$arResult["ORDER"]["ITEMDISCOUNT_DISPLAY"]?></div>
		</div>
		<?if($arResult["ORDER"]["DELIVERY_ID"]>0){?>
		<div class="wrapPriceAll">
			<div class="left"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_9")?> (<?=$arResult["DELIVERY"][$arResult["ORDER"]["DELIVERY_ID"]]['NAME']?>):</div>
			<div class="right"><?=$arResult["ORDER"]['DELIVERYCOST_DISPLAY']?></div>
		</div>
		<?}?>
		<?if($arResult["ORDER"]["DELIVERY_ID"]>0){?>
		<div class="wrapPriceAll">
			<div class="left"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_10")?> (<?=$arResult["PAYMENT"][$arResult["ORDER"]["PAYMENT_ID"]]['NAME']?>):</div>
			<div class="right"><?=$arResult["ORDER"]['PAYMENTCOST_DISPLAY']?></div>
		</div>
		<?}?>
		<div class="wrapPriceAll">
			<div class="left"><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_11")?>:</b></div>
			<div class="right"><b><?=$arResult["ORDER"]["ORDERSUM_DISPLAY"]?></b></div>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<h4 class="titleOrder"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_12")?></h4>
	<?if(count($arResult["USERPROPS"])>0){?>
	<table class="userPropsList">
		<?foreach($arResult["USERPROPS"] as $prop){?>
			<?if($prop["TYPE"]=='TEXT' || $prop["TYPE"]=='EMAIL'){?>
			<tr>
				<td class="label"><label for="user_<?=$prop['CODE']?>"><?=$prop['NAME']?><?if($prop["REQ"]=="Y") echo '<font style="color:red;">*</font>';?></label></td>
				<td class="field"><input type="text" name="user_<?=$prop['CODE']?>" id="user_<?=$prop['CODE']?>" value="<?=$prop['VALUE']?>"/></td>
			</tr>
			<?}elseif($prop["TYPE"]=='TEXTAREA'){?>
			<tr>
				<td class="label"><label for="user_<?=$prop['CODE']?>"><?=$prop['NAME']?><?if($prop["REQ"]=="Y") echo '<font style="color:red;">*</font>';?></label></td>
				<td class="field"><textarea type="text" name="user_<?=$prop['CODE']?>" id="user_<?=$prop['CODE']?>"><?=$prop['VALUE']?></textarea></td>
			</tr>
			<?}elseif($prop["TYPE"]=='LOCATION'){?>
			<tr>
				<td class="label"><label for="user_<?=$prop['CODE']?>"><?=$prop['NAME']?><?if($prop["REQ"]=="Y") echo '<font style="color:red;">*</font>';?></label></td>
				<td class="field">
				<select type="text" name="user_<?=$prop['CODE']?>" id="user_<?=$prop['CODE']?>">
					<?foreach($prop["VALUES"] as $key=>$val){?>
						<option value="<?=$key?>"<?if($key==$prop["VALUE"]) echo ' selected="selected"';?>><?=$val?></option>
					<?}?>
				</select>
				</td>
			</tr>
			<?}?>
		<?}?>
	</table>
	<?}?>
	<div class="clear"></div>
	<h4 class="titleOrder"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_13")?></h4>
	<input type="hidden" name="DELIVERY_ID" id="DELIVERY_ID" value="<?=$arResult["ORDER"]["DELIVERY_ID"]?>"/>
	<table class="deliveryItems">
		<?if(count($arResult["DELIVERY"])>0){?>
		<?foreach($arResult["DELIVERY"] as $deliver){?>
		<tr>
			<td class="image"><img class="<?if($deliver['ID']==$arResult["ORDER"]["DELIVERY_ID"]) echo 'active';?>" data-id="<?=$deliver['ID']?>" src="<?=$deliver['IMAGE']?>"/></td>
			<td class="desc">
			<p class="name"><?=$deliver['NAME']?></p>
			<p><?=$deliver['DESC']?></p>
			<p><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_14")?>:</b> <?=$deliver['COST_DISPLAY']?></p>
			</td>
		</tr>
		<?}?>
		<?}else{?>
		<tr>
			<td class="image"></td>
			<td class="desc"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_15")?></td>
		</tr>
		<?}?>
	</table>
	<div class="clear"></div>
	<h4 class="titleOrder"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_16")?></h4>
	<input type="hidden" name="PAYMENT_ID" id="PAYMENT_ID" value="<?=$arResult["ORDER"]["PAYMENT_ID"]?>"/>
	<table class="paymentItems">
		<?if(count($arResult["PAYMENT"])>0){?>
		<?foreach($arResult["PAYMENT"] as $payment){?>
		<tr>
			<td class="image"><img class="<?if($payment['ID']==$arResult["ORDER"]["PAYMENT_ID"]) echo 'active';?>" data-id="<?=$payment['ID']?>" src="<?=$payment['IMAGE']?>"/></td>
			<td class="desc">
			<p class="name"><?=$payment['NAME']?></p>
			<p><?=$payment['DESC']?></p>
			<p><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_17")?>:</b> <?=$payment['COST_DISPLAY']?></p>
			</td>
		</tr>
		<?}?>
		<?}else{?>
		<tr>
			<td class="image"></td>
			<td class="desc"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_18")?></td>
		</tr>
		<?}?>
	</table>
	
	<div class="clear"></div>
	
	
	<input type="hidden" name="orderfin" id="orderfin" value=""/>
	<div class="errorsOrder">
		<?if(count($arResult['ORDER_ERROR'])>0){?>
		<?=implode(', ',$arResult['ORDER_ERROR'])?>
		<?}?>
	</div>
	<div class="buttons">
		<a href="#"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_19")?></a>
	</div>
</form>
<?
}elseif($arResult['ORDER_CREATE']){?>
<div class="textOrder">
	<p><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_20")?> <b><?=$arResult["ORDERID"]?></b> <?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_27")?> <?=date("d.m.Y")?>, <?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_21")?>. </p>
	<p><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_22")?>:</b> <?=$arResult["ORDER"]["ORDERSUM_DISPLAY"]?></p>
	<p><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_23")?></p>
	<p><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_24")?>:</b> <font style="color:red"><?=$arResult["ORDERID"]?></font>
	<br/><b><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_25")?>:</b> <font style="color:red"><?=$arResult["ORDERPASS"]?></font></p>
	<?
	$cl = "\\Mlife\\Asz\\Payment\\".$arResult['PAYMENT'][$arResult["ORDER"]["PAYMENT_ID"]]["ACTIONFILE"];
	if($arResult['PAYMENT'][$arResult["ORDER"]["PAYMENT_ID"]]["ACTIONFILE"] && class_exists($cl)){
		echo $cl::getPayButton($arResult["ORDER"]["PAYMENT_ID"]);
	}
	?>
</div>
<?}else{?>
<div class="errorBasket"><?=GetMessage("MLIFE_ASZ_BASKET_FULL_T_26")?></div>
<?
}
?>
<?if($_REQUEST['ajaxrefresh']==1){
die();
}?>
</div>
<?//echo '<pre style="font-size:12px;">';print_r($arResult); echo '</pre>';?>