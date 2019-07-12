<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
<div class="order-full-summary">
	<div class="order-item">
		<div class="order-title">
			<b class="r2"></b><b class="r1"></b><b class="r0"></b>
			<div class="order-title-inner">
				<span><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ID"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></span>
			</div>
		</div>
		<div class="order-info">
			<table class="order-properties" cellspacing="0">
				<tbody><tr>
					<td class="field-name"><?echo GetMessage("SPOD_ORDER_STATUS")?></td>
					<td class="field-value"><?=$arResult["STATUS"]["NAME"]?><?=GetMessage("SPOD_ORDER_FROM")?><?=$arResult["DATE_STATUS"]?>)</td>
				</tr>
				<tr>
					<td class="field-name"><?=GetMessage("P_ORDER_PRICE")?></td>
					<td class="field-value"><?
						echo "<b>".$arResult["PRICE_FORMATED"]."</b>";
						if (DoubleVal($arResult["SUM_PAID"]) > 0)
							echo "(".GetMessage("SPOD_ALREADY_PAID")."&nbsp;<b>".$arResult["SUM_PAID_FORMATED"]."</b>)";
						?></td>
				</tr>
				<tr>
					<td class="field-name"><?= GetMessage("P_ORDER_CANCELED") ?>:</td>
					<td class="field-value"><?echo (($arResult["CANCELED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
						if ($arOrder["CANCELED"] == "Y")
						{
							echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_CANCELED"].")";
							if (strlen($arResult["REASON_CANCELED"]) > 0)
								echo "<br />".$arResult["REASON_CANCELED"];
						}
						elseif ($arResult["CAN_CANCEL"]=="Y")
						{
							?>&nbsp;<a href="<?=$arResult["URL_TO_CANCEL"]?>"><?=GetMessage("SALE_CANCEL_ORDER")?></a><?
						}?></td>
				</tr>
			</tbody></table>
		</div>
	</div>

	<?if (IntVal($arResult["USER_ID"])>0):?>
		<div class="order-item">
			<div class="order-title">
				<b class="r2"></b><b class="r1"></b><b class="r0"></b>
				<div class="order-title-inner">
					<span><?echo GetMessage("SPOD_ACCOUNT_DATA")?></span>
				</div>
			</div>
			<div class="order-info">
				<table class="order-properties" cellspacing="0">
					<tbody>
					<?if(strlen($arResult["USER_NAME"]) > 0):?>
						<tr>
							<td class="field-name"><?= GetMessage("SPOD_ACCOUNT") ?>:</td>
							<td class="field-value"><?=$arResult["USER_NAME"]?></td>
						</tr>
					<?endif;?>
					<tr>
						<td class="field-name"><?= GetMessage("SPOD_LOGIN") ?></td>
						<td class="field-value"><?=$arResult["USER"]["LOGIN"]?></td>
					</tr>
					<tr>
						<td class="field-name"><?echo GetMessage("SPOD_EMAIL")?></td>

						<td class="field-value"><a href="mailto:<?=$arResult["USER"]["EMAIL"]?>"><?=$arResult["USER"]["EMAIL"]?></a></td>
					</tr>
				</tbody></table>
			</div>
		</div>
	<?endif;?>
	
	<div class="order-item">
	<div class="order-title">
		<b class="r2"></b><b class="r1"></b><b class="r0"></b>
		<div class="order-title-inner">
			<span><?=GetMessage("P_ORDER_USER")?></span>
		</div>
	</div>
	<div class="order-info">
		<table class="order-properties" cellspacing="0">
			<tbody><?if(!empty($arResult["ORDER_PROPS"]))
			{
				foreach($arResult["ORDER_PROPS"] as $val)
				{
					if ($val["SHOW_GROUP_NAME"] == "Y")
					{
						?>
						<tr>
							<td colspan="2" class="field-title"><?=$val["GROUP_NAME"];?></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td class="field-name"><?echo $val["NAME"] ?>:</td>
						<td class="field-value"><?
							if ($val["TYPE"] == "CHECKBOX")
							{
								if ($val["VALUE"] == "Y")
									echo GetMessage("SALE_YES");
								else
									echo GetMessage("SALE_NO");
							}
							else
								echo $val["VALUE"];
							?></td>
					</tr>
					<?
				}
			}
			if (strlen($arResult["USER_DESCRIPTION"])>0)
			{
				?>
				<tr>
					<td class="field-name"><?=GetMessage("P_ORDER_USER_COMMENT")?>:</td>
					<td class="field-value"><?=$arResult["USER_DESCRIPTION"]?></td>
				</tr>
				<?
			}?>
			</tbody></table>
		</div>
	</div>
	
	<div class="order-item">
	<div class="order-title">
		<b class="r2"></b><b class="r1"></b><b class="r0"></b>
		<div class="order-title-inner">
			<span><?=GetMessage("P_ORDER_PAYMENT")?></span>
		</div>
	</div>
	<div class="order-info">
		<table class="order-properties" cellspacing="0">
			<tbody><tr>
				<td class="field-name"><?=GetMessage("P_ORDER_PAY_SYSTEM")?>:</td>
				<td class="field-value"><?
					if (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
						echo $arResult["PAY_SYSTEM"]["NAME"];
					else
						echo GetMessage("SPOD_NONE");
					?></td>
			</tr>
			<tr>
				<td class="field-name"><?echo GetMessage("P_ORDER_PAYED") ?>:</td>
				<td class="field-value"><?
					echo (($arResult["PAYED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
					if ($arResult["PAYED"] == "Y")
						echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_PAYED"].")";
					if ($arResult["CAN_REPAY"]=="Y")
					{
						if ($arResult["PAY_SYSTEM"]["PSA_NEW_WINDOW"] == "Y")
						{
							?>
							<a href="<?=$arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"]?>" target="_blank"><?=GetMessage("SALE_REPEAT_PAY")?></a>
							<?
						}
						else
						{
							$ORDER_ID = $ID;
							include($arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"]);
						}
					}?>
					</td>
			</tr>
			<tr>
				<td class="field-name"><?=GetMessage("P_ORDER_DELIVERY")?>:</td>
				<td class="field-value"><?
					if (strpos($arResult["DELIVERY_ID"], ":") !== false || IntVal($arResult["DELIVERY_ID"]) > 0)
					{
						echo $arResult["DELIVERY"]["NAME"];
					}
					else
					{
						echo GetMessage("SPOD_NONE");
					}
					?></td>
			</tr>
		</tbody></table>
	</div>
	</div>

	<div class="order-item">
		<div class="order-title">
			<b class="r2"></b><b class="r1"></b><b class="r0"></b>
			<div class="order-title-inner">
				<span><?=GetMessage("P_ORDER_BASKET")?></span>
			</div>
		</div>
		<div class="order-info">
			<div class="cart-items">
				<table class="cart-items" cellspacing="0">
					<thead>
						<tr>
							<td class="cart-item-name"><?= GetMessage("SPOD_NAME") ?></td>
							<td class="cart-item-price"><?= GetMessage("SPOD_PRICE") ?></td>
							<td class="cart-item-price"><?= GetMessage("SPOD_VAT") ?></td>
							<td class="cart-item-weight"><?= GetMessage("SPOD_WEIGHT") ?></td>
							<td class="cart-item-quantity"><?= GetMessage("SPOD_QUANTITY") ?></td>
						</tr>
					</thead>
					<tbody>
					<?
					foreach($arResult["BASKET"] as $val)
					{
						?>
						<tr>
							<td class="cart-item-name"><?
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "<a href=\"".$val["DETAIL_PAGE_URL"]."\">";
							echo htmlspecialcharsEx($val["NAME"]);
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "</a>";

							if(!empty($val["PROPS"]))
							{
								foreach($val["PROPS"] as $vv) 
									echo "<p>".$vv["NAME"].": ".$vv["VALUE"]."</p>";
							}?></td>
							<td class="cart-item-price"><?=$val["PRICE_FORMATED"]?></td>
							<td class="cart-item-price"><?=$val["VAT_RATE"]*100?>%</td>
							<td class="cart-item-weight"><?=$val["WEIGHT_FORMATED"]?></td>
							<td class="cart-item-quantity"><?=$val["QUANTITY"]?></td>
						</tr>
						<?
					}
					?>
					</tbody>
					<tfoot>									    
						<tr>
							<td class="cart-item-name">
								<?
								if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0)
									echo "<p>".GetMessage("SPOD_WEIGHT_ALL").":</p>";

								echo "<p>".GetMessage("SPOD_CLEAR_PRICE").":</p>";
								if(DoubleVal($arResult["TAX_VALUE"]) > 0)
									echo "<p>".GetMessage("SPOD_TAX").":</p>";
								if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0)
									echo "<p>".GetMessage("SPOD_DISCOUNT").":</p>";
								if(DoubleVal($arResult["PRICE_DELIVERY"]) > 0)
									echo "<p>".GetMessage("SPOD_DELIVERY").":</p>";
								?>
								<p><b><?=GetMessage("SPOD_ITOG")?>:</b></p>
							</td>
							<td class="cart-item-price">
								<?
								if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0)
									echo "<p>".$arResult["ORDER_WEIGHT_FORMATED"]."</p>";
								?><p><?= SaleFormatCurrency($arResult["PRICE"] - $arResult["TAX_VALUE"]-$arResult["PRICE_DELIVERY"], $arResult["CURRENCY"])?></p><?
								if(DoubleVal($arResult["TAX_VALUE"]) > 0)
									echo "<p>".$arResult["TAX_VALUE_FORMATED"]."</p>";
								if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0)
									echo "<p>".$arResult["DISCOUNT_VALUE_FORMATED"]."</p>";
								if(DoubleVal($arResult["PRICE_DELIVERY"]) > 0)
									echo "<p>".$arResult["PRICE_DELIVERY_FORMATED"]."</p>";
								?>
								<p><b><?=$arResult["PRICE_FORMATED"]?></b></p>
							</td>
							<td class="cart-item-weight">&nbsp;</td>
							<td class="cart-item-weight">&nbsp;</td>
							<td class="cart-item-quantity">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
