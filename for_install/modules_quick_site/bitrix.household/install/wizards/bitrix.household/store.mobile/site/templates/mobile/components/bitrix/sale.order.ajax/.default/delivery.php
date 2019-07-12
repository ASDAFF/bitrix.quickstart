<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["DELIVERY"]))
{
	?>
	<div class="order-item">
		<div class="order-title">
			<div class="order-title-inner">
				<span><?=GetMessage("SOA_TEMPL_DELIVERY")?></span>
			</div>
		</div>
		<div class="order-info">
		<table width="100%" cellpadding="6" cellspacing="0">
		<?
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
				{
					?>
					<tr>
						<td width="0%" valign="top"><input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>/></td>
						<td width="100%"><label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
								<b><?=$arDelivery["TITLE"]?> - <?=$arProfile["TITLE"]?></b><?if (strlen($arProfile["DESCRIPTION"]) > 0):?><br /><?=nl2br($arProfile["DESCRIPTION"])?><?endif;?></label><br />
						<?
							$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
								"NO_AJAX" => 'Y',
								"DELIVERY" => $delivery_id,
								"PROFILE" => $profile_id,
								"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],                                                                                               
								"ORDER_PRICE" => $arResult["ORDER_PRICE"],
								"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
								"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
								"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
							));
						?>
						</td>
					</tr>
					<script>
					   $('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').live("change",function() {
							submitForm();
						});
					</script>
					<?
				} // endforeach
			}	
			else
			{
				?>
				<tr>
					<td valign="top" width="0%">
						<input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>></td>
						<td width="100%">
						<label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
						<b><?= $arDelivery["NAME"] ?></b></label><br clear="all">
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
					</td>
				</tr>
				<script>
				   $('#ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>').live("change",function() {
						submitForm();
					});
				</script>
				<?
			}
		}
		?>
	</table>
	</div>
	</div>
	<?
}
?>