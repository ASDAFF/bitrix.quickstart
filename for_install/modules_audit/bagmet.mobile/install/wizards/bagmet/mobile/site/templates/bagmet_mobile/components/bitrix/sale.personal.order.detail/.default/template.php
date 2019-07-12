<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
<div class="order_wrapper">
	<div class=" my_orders_item_details">

		<h4><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ID"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></h4>
		<a  class="my_orders_item_to_details" href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SPOD_RECORDS_LIST")?></a>
		<table class="my_order_details">
			<tbody>
			<tr>
				<td><strong><?echo GetMessage("SPOD_ORDER_STATUS")?></strong></td>
				<td><?=$arResult["STATUS"]["NAME"]?><?=GetMessage("SPOD_ORDER_FROM")?><?=$arResult["DATE_STATUS"]?>)</td>
			</tr>
			<tr>
				<td><strong><?=GetMessage("P_ORDER_PRICE")?>:</strong></td>
				<td><?
					echo "<b>".$arResult["PRICE_FORMATED"]."</b>";
					if (DoubleVal($arResult["SUM_PAID"]) > 0)
						echo "(".GetMessage("SPOD_ALREADY_PAID")."&nbsp;<b>".$arResult["SUM_PAID_FORMATED"]."</b>)";
					?></td>
			</tr>
			<tr>
				<td><strong><?= GetMessage("P_ORDER_CANCELED") ?>:</strong></td>
				<td><?echo (($arResult["CANCELED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
					if ($arResult["CANCELED"] == "Y")
					{
						echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_CANCELED"].")";
						if (strlen($arResult["REASON_CANCELED"]) > 0)
							echo "<br />".$arResult["REASON_CANCELED"];
					}
					elseif ($arResult["CAN_CANCEL"]=="Y")
					{
						?>&nbsp;<a href="<?=$arResult["URL_TO_CANCEL"]?>"><?=GetMessage("SALE_CANCEL_ORDER")?></a><?
					}?>
				</td>
			</tr>


		<?if (IntVal($arResult["USER_ID"])>0):?>
			<tr>
				<td colspan="2"><h5><?=GetMessage("SPOD_ACCOUNT_DATA")?></h5></td>
			</tr>
			<?if(strlen($arResult["USER_NAME"]) > 0):?>
				<tr>
					<td><strong><?=GetMessage("SPOD_ACCOUNT") ?>:</strong></td>
					<td><?=$arResult["USER_NAME"]?></td>
				</tr>
			<?endif;?>
			<tr>
				<td><strong><?= GetMessage("SPOD_LOGIN") ?></strong></td>
				<td><?=$arResult["USER"]["LOGIN"]?></td>
			</tr>
			<tr>
				<td><strong><?echo GetMessage("SPOD_EMAIL")?></strong></td>
				<td><a href="mailto:<?=$arResult["USER"]["EMAIL"]?>"><?=$arResult["USER"]["EMAIL"]?></a></td>
			</tr>
		<?endif;?>

			<tr>
				<td colspan="2"><h5><?=GetMessage("P_ORDER_USER")?></h5></td>
			</tr>
			<?if(!empty($arResult["ORDER_PROPS"]))
			{
				foreach($arResult["ORDER_PROPS"] as $val)
				{
					if ($val["SHOW_GROUP_NAME"] == "Y")
					{
						?>
						<tr>
							<td colspan="2"><strong><?=$val["GROUP_NAME"];?></strong></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td><strong><?echo $val["NAME"] ?>:</strong></td>
						<td><?
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
					<td><strong><?=GetMessage("P_ORDER_USER_COMMENT")?>:</strong></td>
					<td><?=$arResult["USER_DESCRIPTION"]?></td>
				</tr>
				<?
			}?>

			<tr>
				<td colspan="2"><h5><?=GetMessage("P_ORDER_PAYMENT")?></h5></td>
			</tr>
			<tr>
				<td><strong><?=GetMessage("P_ORDER_PAY_SYSTEM")?>:</strong></td>
				<td><?
					if (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
						echo $arResult["PAY_SYSTEM"]["NAME"];
					else
						echo GetMessage("SPOD_NONE");
					?>
				</td>
			</tr>
			<tr>
				<td class="td_vertical_top"><strong><?echo GetMessage("P_ORDER_PAYED") ?>:</strong></td>
				<td><?
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
				<td><strong><?=GetMessage("P_ORDER_DELIVERY")?>:</strong></td>
				<td><?
					if (strpos($arResult["DELIVERY_ID"], ":") !== false || IntVal($arResult["DELIVERY_ID"]) > 0)
					{
						echo $arResult["DELIVERY"]["NAME"];
					}
					else
					{
						echo GetMessage("SPOD_NONE");
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<br/><br/><br/>

	<h4><?=GetMessage("P_ORDER_BASKET")?></h4>
	<table class="shopping_cart order_list_table">
		<thead>
			<tr>
				<th class="cart_tovar_img_title"><?= GetMessage("SPOD_NAME") ?></th>
				<th><?= GetMessage("SPOD_PROPS") ?></th>
				<th><?= GetMessage("SPOD_VAT") ?></th>
				<th><?= GetMessage("SPOD_WEIGHT") ?></th>
				<th><?= GetMessage("SPOD_QUANTITY") ?></th>
				<th class="cart_tovar_discount_title"><?= GetMessage("SPOD_PRICE") ?></th>
			</tr>
		</thead>
		<tbody>
		<?
		foreach($arResult["BASKET"] as $val)
		{
			?>
			<tr>
				<td><?
				if (strlen($val["DETAIL_PAGE_URL"])>0)
					echo "<a class='cart_tovar_name' href=\"".$val["DETAIL_PAGE_URL"]."\">";
				echo htmlspecialcharsEx($val["NAME"]);
				if (strlen($val["DETAIL_PAGE_URL"])>0)
					echo "</a>";
				?>
				</td>
				<td align="center">
				<?
				if(!empty($val["PROPS"]))
				{
					foreach($val["PROPS"] as $vv)
					{
						echo "<p class='cart_tovar_other_p'>".$vv["NAME"].": ".$vv["VALUE"]."</p>";
					}

				}?></td>
				<td align="center"><?=$val["VAT_RATE"]*100?>%</td>
				<td align="center"><?=$val["WEIGHT_FORMATED"]?></td>
				<td align="center"><?=$val["QUANTITY"]?></td>
				<td align="right" class="cart_tovar_price_cell"><p class='price'><?=$val["PRICE_FORMATED"]?></p></td>
			</tr>
			<?
		}
		?>
		</tbody>
		<tfoot>
			<?if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0):?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_WEIGHT_ALL").":";?></p></td>
					<td align="right" class="cart_tovar_price_cell"><p class="price_nds"><?=$arResult["ORDER_WEIGHT_FORMATED"];?></p></td>
				</tr>
			<?endif?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_CLEAR_PRICE").":";?></p></td>
				<td align="right" class="cart_tovar_price_cell">
					<p class="price_nds"><?=SaleFormatCurrency($arResult["PRICE"] - $arResult["TAX_VALUE"]-$arResult["PRICE_DELIVERY"], $arResult["CURRENCY"])?></p>
				</td>
			</tr>
			<?if(DoubleVal($arResult["TAX_VALUE"]) > 0):?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_TAX").":";?></p></td>
					<td align="right" class="cart_tovar_price_cell"><p class="price_nds"><?=$arResult["TAX_VALUE_FORMATED"];?></p></td>
				</tr>
			<?endif?>
			<?if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0):?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_DISCOUNT").":";?></p></td>
					<td align="right" class="cart_tovar_price_cell"><p class="price_nds"><?=$arResult["DISCOUNT_VALUE_FORMATED"];?></p></td>
				</tr>
			<?endif?>
			<?if(DoubleVal($arResult["PRICE_DELIVERY"]) > 0):?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_DELIVERY").":";?></p></td>
					<td align="right" class="cart_tovar_price_cell"><p class="price_nds"><?=$arResult["PRICE_DELIVERY_FORMATED"];?></p></td>
				</tr>
			<?endif?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="price_descr_container"><p class="price_nds_title"><?=GetMessage("SPOD_ITOG")?>:</p></td>
				<td align="right" class="cart_tovar_price_cell"><p class='price_total'><?=$arResult["PRICE_FORMATED"]?></p></td>
			</tr>
		</tfoot>
	</table>
	</div>
</div>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
