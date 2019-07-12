<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["DELIVERY"]))
{
	?>
	<h2><?=GetMessage("SOA_TEMPL_DELIVERY")?></h2>
		<div class="order-info">
		<table>
		<?
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
				{
					?>
					<tr>
						<td style="vertical-align:top;padding:0 5px;"><input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> onclick="submitForm();" /></td>
						<td>
							<?=$arDelivery["TITLE"]?> - <?=$arProfile["TITLE"]?></b>
							<p style="font-weight:normal;">
							<?if (strlen($arProfile["DESCRIPTION"]) > 0)
							{
								echo nl2br($arProfile["DESCRIPTION"]);
							}
							?>
							<?
								$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
									"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
									"DELIVERY" => $delivery_id,
									"PROFILE" => $profile_id,
									"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
									"ORDER_PRICE" => $arResult["ORDER_PRICE"],
									"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
									"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
									"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
								));
							?>
							</p>
						</td>
					</tr>
					<?
				} // endforeach
			}	
			else
			{
				?>
				<tr>
					<td style="vertical-align:top;padding:0 5px;">
						<input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?> onclick="submitForm();">
					</td>
					<td>
						<label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"><?=$arDelivery["NAME"] ?></label>
                        <p style="font-weight:normal;">
						<?
						if (strlen($arDelivery["PERIOD_TEXT"])>0)
						{
							echo $arDelivery["PERIOD_TEXT"];
							?><br /><?
						}
						
						if(DoubleVal($arDelivery["PRICE"]) > 0)
						{
							echo GetMessage("SALE_DELIV_PRICE")." ".$arDelivery["PRICE_FORMATED"]."<br />";
						}
						if (strlen($arDelivery["DESCRIPTION"])>0)
						{
							?>
							<?=$arDelivery["DESCRIPTION"]?><br />
							<?
						}
						?>
						</p>
					</td>
				</tr>
				<?
			}
		}
		?>
	</table>
	</div>
	<?
}
?>