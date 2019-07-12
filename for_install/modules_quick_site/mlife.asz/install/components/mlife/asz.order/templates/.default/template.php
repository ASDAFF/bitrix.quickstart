<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?if($arResult['ERROR']){?>
<h1><?=GetMessage("MLIFE_ASZ_ORDER_T_H1")?><?if($arResult['ORDER_ID']>0){?> <?=GetMessage("MLIFE_ASZ_ORDER_T_NUM")?>: <?=$arResult['ORDER_ID']?><?}?></h1>
<?if(intval($_REQUEST["ID"])){?>
<div class="orderDetailerror"><?=$arResult['ERROR']?></div>
<?}?>
<div class="formAutZakaz"><form method="GET">
	<div class="field"><label><?=GetMessage("MLIFE_ASZ_ORDER_T_NUMZAK")?>:</label><input type="text" name="ID" value="<?if(intval($_REQUEST["ID"])){?><?=intval($_REQUEST["ID"])?><?}?>"/></div>
	<div class="field"><label><?=GetMessage("MLIFE_ASZ_ORDER_T_PASSW")?>:</label><input type="text" name="PASS" value=""/></div>
	<div class="button"><input type="submit" value="<?=GetMessage("MLIFE_ASZ_ORDER_T_SEND")?>"/></div>
</form></div>
<?}else{?>
<h1><?=GetMessage("MLIFE_ASZ_ORDER_T_H1")?><?if($arResult['ORDER_ID']>0){?> <?=GetMessage("MLIFE_ASZ_ORDER_T_NUM")?>: <?=$arResult['ORDER_ID']?><?}?></h1>
<div class="orderDetail">
	<div class="wrapBlockOrder">
	<h3 class="titler"><?=GetMessage("MLIFE_ASZ_ORDER_T_DANNIE")?>:</h3>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_NUMZAK")?>:</div>
		<div class="right"><?=$arResult['ORDER_ID']?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_PASSW")?>:</div>
		<div class="right"><?=$arResult['ORDERDATA']["PASSW"]?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_DATA")?>:</div>
		<div class="right"><?=ConvertTimeStamp($arResult['ORDERDATA']['DATE'],"FULL",SITE_ID)?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_STATUS")?>:</div>
		<div class="right"><b><?=$arResult['ORDERDATA']['STATUS_DATA']['NAME']?></b><br/><?=$arResult['ORDERDATA']['STATUS_DATA']['DESC']?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_DELIVERY")?>:</div>
		<div class="right"><b><?=$arResult['ORDERDATA']['DELIVERY_DATA']['NAME']?></b><br/><?=$arResult['ORDERDATA']['DELIVERY_DATA']['DESC']?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_PAYMENT")?>:</div>
		<div class="right"><b><?=$arResult['ORDERDATA']['PAY_DATA']['NAME']?></b><br/><?=$arResult['ORDERDATA']['PAY_DATA']['DESC']?></div>
	</div>
	<div class="rowZakaz">
		<div class="left"><?=GetMessage("MLIFE_ASZ_ORDER_T_SUMM")?>:</div>
		<div class="right">
		<?=$arResult['ORDER']['ORDERSUM_DISPLAY']?>
		<?
		$cl = "\Mlife\\Asz\\Payment\\".$arResult["ORDERDATA"]["PAY_DATA"]["ACTIONFILE"];
		if($arResult["ORDERDATA"]["PAY_DATA"]["ACTIONFILE"] && class_exists($cl)){
			echo $cl::getPayButton($arResult["ORDERDATA"]["ID"]);
		}?>
		</div>
	</div>
	</div>
	<div class="wrapBlockOrder">
	<h3 class="titler"><?=GetMessage("MLIFE_ASZ_ORDER_T_DANNIE2")?>:</h3>
	<?foreach($arResult["USERPROPS"] as $val){?>
		<div class="rowZakaz">
			<div class="left"><?=$val["NAME"]?>:</div>
			<div class="right"><?=$val["VALUE"]?></div>
		</div>
	<?}?>
	</div>
	<div class="wrapBlockOrder">
	<h3 class="titler"><?=GetMessage("MLIFE_ASZ_ORDER_T_SOSTAV")?>:</h3>
	<table class="productWrap">
		<tr>
			<th><?=GetMessage("MLIFE_ASZ_ORDER_T_NAMETD")?></th>
			<th><?=GetMessage("MLIFE_ASZ_ORDER_T_PRICETD")?></th>
			<th><?=GetMessage("MLIFE_ASZ_ORDER_T_DISCOUNTTD")?></th>
			<th><?=GetMessage("MLIFE_ASZ_ORDER_T_KOLTD")?></th>
			<th class="finprice"><?=GetMessage("MLIFE_ASZ_ORDER_T_PRICE2TD")?></th>
		</tr>
		<?foreach($arResult["BASKET_ITEMS"] as $item){?>
			<tr>
				<td><?=$item["PROD_NAME"]?><?if($item["PROD_DESC"]){?><br/><?=$item["PROD_DESC"]?><?}?></td>
				<td><?=$item["PRICE_DISPLAY"]?></td>
				<td><?=$item["DISCOUNT_DISPLAY"]?></td>
				<td><?=round($item["QUANT"])?></td>
				<td><?=$item["PRICE_DISPLAY_ALL"]?></td>
			</tr>
		<?}?>
	</table>
	<table class="itogWrap">
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_PRICEORDER")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["ITEMSUM_DISPLAY"]?>
			</td>
		</tr>
		<?if($arResult["ORDER"]["ITEMDISCOUNT"]>0){?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_DELIVERYORDER")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["ITEMDISCOUNT_DISPLAY"]?>
			</td>
		</tr>
		<?}?>
		<?if($arResult["ORDER"]["DISCOUNT"]>0){?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_DELIVERYORDER2")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["DISCOUNT_DISPLAY"]?>
			</td>
		</tr>
		<?}?>
		<?if($arResult["ORDER"]["ORDERTAX"]>0){?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_TAXORDER")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["ORDERTAX_DISPLAY"]?>
			</td>
		</tr>
		<?}?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_DELIVERYORDER3")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["DELIVERYCOST_DISPLAY"]?>
			</td>
		</tr>
		<?if($arResult["ORDER"]["PAYMENTCOST"]>0){?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_PAYORDER2")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["PAYMENTCOST_DISPLAY"]?>
			</td>
		</tr>
		<?}?>
		<tr>
			<td class="left">
				<?=GetMessage("MLIFE_ASZ_ORDER_T_ITOG")?>:
			</td>
			<td class="right">
				<?=$arResult["ORDER"]["ORDERSUM_DISPLAY"]?>
			</td>
		</tr>
	</table>
	</div>
</div>
<?}?>